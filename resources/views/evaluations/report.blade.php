@extends('layouts.app')

@section('title', 'Laporan Hasil Perhitungan')

@section('content')
<div class="card" style="border-left: 6px solid var(--primary); background: linear-gradient(to right, #ffffff, #f8fafc);">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h2 style="font-weight: 800; color: var(--text-main); margin: 0; font-size: 1.5rem;">Analisis Metode SAW</h2>
            <p style="color: var(--text-muted); margin-top: 0.5rem; font-size: 0.9375rem;">Hasil perhitungan performa karyawan periode <span style="color: var(--primary); font-weight: 700;">{{ $period }}</span></p>
        </div>
        <div style="display: flex; gap: 0.75rem;">
            <button id="downloadPdfBtn" type="button" class="btn btn-primary">
                <i class="fas fa-file-pdf"></i> <span id="downloadPdfBtnLabel">Download PDF</span>
            </button>
            <a href="{{ route('evaluations.index') }}" class="btn" style="background: #f1f5f9; color: var(--text-main); border: 1px solid #e2e8f0;"><i class="fas fa-arrow-left"></i> Kembali</a>
        </div>
    </div>
</div>

<div id="report-pdf-content">

<!-- Step 1: Decision Matrix -->
<div class="card">
    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 1.5rem;">
        <div style="width: 40px; height: 40px; background: #eff6ff; color: #3b82f6; border-radius: 10px; display:flex; align-items:center; justify-content:center; font-weight:800;">1</div>
        <h3 style="font-weight: 700; color: var(--text-main); margin: 0;">Matriks Keputusan (Decision Matrix - X)</h3>
    </div>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th rowspan="2" style="vertical-align: middle;">Alternatif (Karyawan)</th>
                    <th rowspan="2" style="vertical-align: middle;">Penilai (HOD)</th>
                    <th colspan="{{ count($criteria) }}" style="text-align: center; background: #f1f5f9;">Kriteria</th>
                </tr>
                <tr>
                    @foreach($criteria as $c)
                        <th style="text-align: center; border-top: 1px solid #e2e8f0;">{{ $c->code }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($evaluations as $eval)
                <tr>
                    <td style="font-weight: 700; color: #334155;">{{ $eval->employee->name }}</td>
                    <td style="color: var(--text-muted);">{{ $eval->evaluator->name }}</td>
                    @foreach($criteria as $c)
                        <td style="text-align: center;">{{ $matrix[$eval->id][$c->id] ?? 0 }}</td>
                    @endforeach
                </tr>
                @endforeach
                <tr style="background: #f8fafc; font-weight: 700;">
                    <td colspan="2" style="color: var(--primary);">Statistik (Max/Min)</td>
                    @foreach($criteria as $c)
                        <td style="text-align: center; color: var(--primary);">
                            @if($c->type == 'benefit')
                                <small style="display:block; font-size: 0.7rem; color: var(--text-muted);">MAX</small>
                                {{ !empty($matrix) && !empty(array_column($matrix, $c->id)) ? max(array_column($matrix, $c->id)) : 0 }}
                            @else
                                <small style="display:block; font-size: 0.7rem; color: var(--text-muted);">MIN</small>
                                {{ !empty($matrix) && !empty(array_column($matrix, $c->id)) ? min(array_column($matrix, $c->id)) : 0 }}
                            @endif
                        </td>
                    @endforeach
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Step 2: Normalization Matrix -->
<div class="card">
    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 1.5rem;">
        <div style="width: 40px; height: 40px; background: #ecfdf5; color: #10b981; border-radius: 10px; display:flex; align-items:center; justify-content:center; font-weight:800;">2</div>
        <h3 style="font-weight: 700; color: var(--text-main); margin: 0;">Matriks Ternormalisasi (Normalized Matrix - R)</h3>
    </div>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Alternatif</th>
                    <th>Penilai</th>
                    @foreach($criteria as $c)
                        <th style="text-align: center;">{{ $c->code }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($evaluations as $eval)
                <tr>
                    <td style="font-weight: 700; color: #334155;">{{ $eval->employee->name }}</td>
                    <td style="color: var(--text-muted);">{{ $eval->evaluator->name }}</td>
                    @foreach($criteria as $c)
                        <td style="text-align: center; color: #059669; font-weight: 700; font-family: 'JetBrains Mono', monospace;">
                            {{ number_format($normalizedMatrix[$eval->id][$c->id], 4) }}
                        </td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Step 3: Weights & Preferences -->
<div class="stat-grid">
    <div class="card" style="margin-bottom: 0; grid-column: span 2;">
        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 2rem;">
            <div style="width: 40px; height: 40px; background: #fff7ed; color: #f59e0b; border-radius: 10px; display:flex; align-items:center; justify-content:center; font-weight:800;">3</div>
            <h3 style="font-weight: 700; color: var(--text-main); margin: 0;">Bobot Preferensi (Weight - W)</h3>
        </div>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 1rem;">
            @foreach($criteria as $c)
            <div style="padding: 1.25rem; border-radius: 12px; background: #f8fafc; border: 1px solid #e2e8f0; text-align: center;">
                <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 700; margin-bottom: 0.5rem;">{{ $c->code }}</div>
                <div style="font-size: 1.25rem; font-weight: 800; color: #b45309;">{{ number_format($c->weight, 2) }}</div>
                <div style="font-size: 0.7rem; color: #d97706; font-weight: 600; margin-top: 0.25rem;">{{ strtoupper($c->type) }}</div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Step 4: Final Rankings -->
<div class="card" style="border-top: 6px solid var(--secondary);">
    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 2rem;">
        <div style="width: 40px; height: 40px; background: #f5f3ff; color: #8b5cf6; border-radius: 10px; display:flex; align-items:center; justify-content:center; font-weight:800;">4</div>
        <h3 style="font-weight: 700; color: var(--text-main); margin: 0;">Hasil Akhir & Perankingan (Preference Value - V)</h3>
    </div>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th style="width: 100px;">Rank</th>
                    <th>Alternatif (Karyawan)</th>
                    <th style="text-align: center;">Rincian Skor (Σ Nilai HOD)</th>
                    <th style="text-align: right;">Total Skor Akhir</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rankingData as $index => $data)
                <tr style="{{ $index == 0 ? 'background: #f5f3ff44;' : '' }}">
                    <td>
                        <div style="width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 800; 
                            {{ $index == 0 ? 'background: linear-gradient(135deg, #f59e0b, #d97706); color: white; box-shadow: 0 4px 10px rgba(217, 119, 6, 0.3);' : 
                               ($index == 1 ? 'background: #cbd5e1; color: white;' : 
                               ($index == 2 ? 'background: #94a3b8; color: white;' : 'background: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0;')) }}">
                            {{ $index + 1 }}
                        </div>
                    </td>
                    <td style="font-weight: 700; font-size: 1rem;">{{ $data['name'] }} @if($index == 0) <i class="fas fa-crown" style="color: #f59e0b; margin-left: 8px;"></i> @endif</td>
                    <td style="text-align: center;">
                        <div style="font-family: 'JetBrains Mono', monospace; font-size: 0.75rem; color: var(--text-muted); background: #f8fafc; padding: 0.5rem; border-radius: 6px; display: inline-block;">
                            @php
                                $parts = [];
                                foreach($data['evaluations'] as $eval) {
                                    $parts[] = "<b>" . $eval->evaluator->name . "</b>: " . number_format($eval->total_score, 4);
                                }
                                echo implode(" + ", $parts);
                            @endphp
                        </div>
                    </td>
                    <td style="text-align: right; font-weight: 800; color: var(--primary); font-size: 1.25rem;">
                        {{ number_format($data['score'], 4) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

</div><!-- /#report-pdf-content -->

@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
    document.getElementById('downloadPdfBtn').addEventListener('click', function () {
        const btn = this;
        const label = document.getElementById('downloadPdfBtnLabel');
        const originalLabel = label.textContent;
        const element = document.getElementById('report-pdf-content');

        btn.disabled = true;
        label.textContent = 'Menyiapkan PDF...';

        const opt = {
            margin:       [10, 8, 10, 8],
            filename:     'Laporan-SAW-{{ $period }}.pdf',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2, useCORS: true },
            jsPDF:        { unit: 'mm', format: 'a4', orientation: 'landscape' },
            pagebreak:    { mode: ['avoid-all', 'css', 'legacy'] }
        };

        html2pdf().set(opt).from(element).save().then(function () {
            btn.disabled = false;
            label.textContent = originalLabel;
        }).catch(function () {
            btn.disabled = false;
            label.textContent = originalLabel;
            alert('Gagal membuat PDF. Silakan coba lagi.');
        });
    });
</script>
@endpush
