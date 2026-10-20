<?php

namespace App\Http\Controllers;

use App\Models\Criterion;
use App\Models\Evaluation;
use App\Models\EvaluationDetail;
use App\Models\User;
use Illuminate\Http\Request;

class EvaluationController extends Controller
{
    public function index()
    {
        $query = Evaluation::with(['employee', 'evaluator'])->latest();

        if (auth()->user()->role === 'hod') {
            $query->where('evaluator_id', auth()->id());
        }

        $evaluations = $query->get();

        return view('evaluations.index', compact('evaluations'));
    }

    public function create()
    {
        $employees = User::where('role', 'employee')
            ->with('department')
            ->get();

        $criteria = Criterion::all();

        return view('evaluations.create', compact('employees', 'criteria'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:users,id',
            'period' => 'required|string',
            'scores' => 'required|array',
            'scores.*' => 'required|numeric|min:0|max:100',
        ]);

        $evaluation = Evaluation::create([
            'employee_id' => $request->employee_id,
            'evaluator_id' => auth()->id(),
            'period' => $request->period,
            'status' => 'pending',
        ]);

        foreach ($request->scores as $criterionId => $score) {
            EvaluationDetail::create([
                'evaluation_id' => $evaluation->id,
                'criterion_id' => $criterionId,
                'score' => $score,
            ]);
        }

        $this->performCalculation($request->period);

        return redirect()
            ->route('evaluations.index')
            ->with(
                'success',
                'Data penilaian berhasil disimpan dan skor dihitung!'
            );
    }

    public function calculate($period)
    {
        $this->performCalculation($period);

        $evaluations = Evaluation::with([
                'details.criterion',
                'employee',
                'evaluator',
            ])
            ->where('period', $period)
            ->orderBy('rank', 'asc')
            ->get();

        if ($evaluations->isEmpty()) {
            return redirect()
                ->route('evaluations.index')
                ->with(
                    'error',
                    'Belum ada data penilaian untuk periode ' . $period
                );
        }

        $criteria = Criterion::all();

        $matrix = [];
        $normalizedMatrix = [];

        foreach ($evaluations as $eval) {
            foreach ($eval->details as $detail) {
                $matrix[$eval->id][$detail->criterion_id] =
                    $detail->score;
            }
        }

        $stats = $this->buildCriterionStats($evaluations);

        foreach ($evaluations as $eval) {
            foreach ($criteria as $criterion) {
                $score = $matrix[$eval->id][$criterion->id] ?? 0;

                $normalizedMatrix[$eval->id][$criterion->id] =
                    $this->normalize(
                        $score,
                        $criterion->type,
                        $stats[$criterion->id] ?? null
                    );
            }
        }

        // Gabungkan nilai penilaian menjadi rata-rata per karyawan.
        $employeeAggregates = [];

        foreach ($evaluations as $eval) {
            $empId = $eval->employee_id;

            if (!isset($employeeAggregates[$empId])) {
                $employeeAggregates[$empId] = [
                    'id' => $eval->id,
                    'name' => $eval->employee->name,
                    'score' => 0,
                    'count' => 0,
                    'evaluations' => [],
                ];
            }

            $employeeAggregates[$empId]['score'] +=
                (float) $eval->total_score;

            $employeeAggregates[$empId]['count']++;

            $employeeAggregates[$empId]['evaluations'][] = $eval;
        }

        $rankingData = [];

        foreach ($employeeAggregates as $data) {
            $data['score'] = $data['count'] > 0
                ? $data['score'] / $data['count']
                : 0;

            $rankingData[] = $data;
        }

        usort(
            $rankingData,
            fn ($a, $b) => $b['score'] <=> $a['score']
        );

        // Tambahkan peringkat pada data laporan.
        foreach ($rankingData as $index => &$data) {
            $data['rank'] = $index + 1;
        }
        unset($data);

        return view('evaluations.report', [
            'evaluations' => $evaluations,
            'criteria' => $criteria,
            'matrix' => $matrix,
            'normalizedMatrix' => $normalizedMatrix,
            'rankingData' => $rankingData,
            'stats' => $stats,
            'period' => $period,
        ]);
    }

    /**
     * Menghitung nilai preferensi SAW dan peringkat karyawan.
     */
    private function performCalculation($period)
    {
        $evaluations = Evaluation::with(['details.criterion'])
            ->where('period', $period)
            ->get();

        if ($evaluations->isEmpty()) {
            return;
        }

        $criteria = Criterion::all();
        $stats = $this->buildCriterionStats($evaluations);

        $employeeTotals = [];

        foreach ($evaluations as $eval) {
            $scoresByCriterion = $eval->details->pluck(
                'score',
                'criterion_id'
            );

            $totalV = 0;

            foreach ($criteria as $criterion) {
                $score = $scoresByCriterion[$criterion->id] ?? 0;

                $rij = $this->normalize(
                    $score,
                    $criterion->type,
                    $stats[$criterion->id] ?? null
                );

                // Bobot harus berupa desimal:
                // 25% = 0.25, 20% = 0.20, dan seterusnya.
                $totalV += ((float) $criterion->weight * $rij);
            }

            $eval->update([
                'total_score' => $totalV,
            ]);

            $empId = $eval->employee_id;

            if (!isset($employeeTotals[$empId])) {
                $employeeTotals[$empId] = [
                    'sum' => 0,
                    'count' => 0,
                ];
            }

            $employeeTotals[$empId]['sum'] += $totalV;
            $employeeTotals[$empId]['count']++;
        }

        // Rata-rata nilai preferensi semua penilai.
        $finalScores = [];

        foreach ($employeeTotals as $empId => $data) {
            $finalScores[$empId] = $data['count'] > 0
                ? $data['sum'] / $data['count']
                : 0;
        }

        arsort($finalScores);

        // Simpan peringkat berdasarkan nilai akhir rata-rata.
        $rank = 1;

        foreach ($finalScores as $empId => $finalScore) {
            Evaluation::where('period', $period)
                ->where('employee_id', $empId)
                ->update([
                    'rank' => $rank,
                ]);

            $rank++;
        }
    }

    /**
     * Menghitung nilai maksimum dan minimum setiap kriteria
     * dalam satu periode.
     */
    private function buildCriterionStats($evaluations): array
    {
        $scores = [];

        foreach ($evaluations as $eval) {
            foreach ($eval->details as $detail) {
                $scores[$detail->criterion_id][] =
                    (float) $detail->score;
            }
        }

        $stats = [];

        foreach ($scores as $criterionId => $list) {
            $stats[$criterionId] = [
                'max' => max($list),
                'min' => min($list),
            ];
        }

        return $stats;
    }

    /**
     * Normalisasi SAW.
     *
     * Benefit: rij = xij / max(xj)
     * Cost   : rij = min(xj) / xij
     */
    private function normalize(
        $score,
        string $type,
        ?array $stat
    ): float {
        $score = (float) $score;

        $stat = $stat ?? [
            'max' => 0,
            'min' => 0,
        ];

        if ($type === 'benefit') {
            return $stat['max'] > 0
                ? $score / $stat['max']
                : 0;
        }

        // Penanganan khusus jika skor cost bernilai nol.
        // Rumus min/x tidak terdefinisi ketika x = 0.
        if ($score == 0) {
            return 1;
        }

        return $stat['min'] / $score;
    }

    public function edit(Evaluation $evaluation)
    {
        if (
            auth()->user()->role === 'hod'
            && $evaluation->evaluator_id !== auth()->id()
        ) {
            abort(403, 'Unauthorized action.');
        }

        $evaluation->load('details');

        $employees = User::where('role', 'employee')
            ->with('department')
            ->get();

        $criteria = Criterion::all();

        $scores = $evaluation->details
            ->pluck('score', 'criterion_id')
            ->toArray();

        return view(
            'evaluations.edit',
            compact('evaluation', 'employees', 'criteria', 'scores')
        );
    }

    public function update(
        Request $request,
        Evaluation $evaluation
    ) {
        if (
            auth()->user()->role === 'hod'
            && $evaluation->evaluator_id !== auth()->id()
        ) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'employee_id' => 'required|exists:users,id',
            'period' => 'required|string',
            'scores' => 'required|array',
            'scores.*' => 'required|numeric|min:0|max:100',
        ]);

        $oldPeriod = $evaluation->period;

        $evaluation->update([
            'employee_id' => $request->employee_id,
            'period' => $request->period,
        ]);

        foreach ($request->scores as $criterionId => $score) {
            EvaluationDetail::updateOrCreate(
                [
                    'evaluation_id' => $evaluation->id,
                    'criterion_id' => $criterionId,
                ],
                [
                    'score' => $score,
                ]
            );
        }

        // Hitung ulang periode lama jika periode berubah.
        if ($oldPeriod !== $request->period) {
            $this->performCalculation($oldPeriod);
        }

        $this->performCalculation($request->period);

        return redirect()
            ->route('evaluations.index')
            ->with(
                'success',
                'Data penilaian berhasil diperbarui dan skor dihitung!'
            );
    }

    public function validateStatus(Evaluation $evaluation)
    {
        $evaluation->update([
            'status' => 'validated',
        ]);

        return redirect()
            ->back()
            ->with(
                'success',
                'Data penilaian berhasil divalidasi dan tersimpan resmi!'
            );
    }

    public function destroy(Evaluation $evaluation)
    {
        if (
            auth()->user()->role === 'hod'
            && $evaluation->evaluator_id !== auth()->id()
        ) {
            abort(403, 'Unauthorized action.');
        }

        $period = $evaluation->period;

        $evaluation->delete();

        $this->performCalculation($period);

        return redirect()
            ->back()
            ->with(
                'success',
                'Data penilaian berhasil dihapus dan skor diperbarui.'
            );
    }
}