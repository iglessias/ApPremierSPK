@extends('layouts.app')

@section('title', 'Manajemen Penilaian')

@section('content')
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2.5rem;">
        <div>
            <h2 style="font-weight: 800; color: var(--text-main); margin: 0; font-size: 1.25rem;">📋 Riwayat Penilaian</h2>
            <p style="color: var(--text-muted); margin: 0.25rem 0 0 0; font-size: 0.8125rem;">Daftar seluruh evaluasi yang telah dicatat dalam sistem.</p>
        </div>
        <div style="display: flex; gap: 0.75rem;">
            @if(auth()->user()->role == 'admin' || auth()->user()->role == 'hod')
            <a href="{{ route('evaluations.create') }}" class="btn" style="background: #10b981; color: white; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);"><i class="fas fa-plus"></i> Tambah Penilaian</a>
            @endif
            @if(auth()->user()->role == 'admin')
            <a href="{{ route('calculate', ['period' => date('Y-m')]) }}" class="btn btn-primary"><i class="fas fa-calculator"></i> Hitung SAW ({{ date('Y-m') }})</a>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div style="background: #f0fdf4; color: #166534; padding: 1rem 1.5rem; border-radius: 10px; margin-bottom: 2rem; border: 1px solid #bbf7d0; font-weight: 600; display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div style="background: #fef2f2; color: #991b1b; padding: 1rem 1.5rem; border-radius: 10px; margin-bottom: 2rem; border: 1px solid #fecaca; font-weight: 600; display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-circle-exclamation"></i> {{ session('error') }}
        </div>
    @endif

    <div style="background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af; padding: 1rem 1.5rem; border-radius: 12px; margin-bottom: 2rem; display: flex; align-items: center; gap: 15px;">
        <div style="background: white; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items:center; justify-content:center; color: #3b82f6; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
            <i class="fas fa-info-circle"></i>
        </div>
        <div>
            <div style="font-weight: 700; font-size: 0.9375rem;">Mengapa Skor Akhir 0.0000?</div>
            <div style="font-size: 0.8125rem; opacity: 0.9;">Skor awal adalah nol. Klik tombol <b>Hitung SAW</b> di atas untuk menjalankan kalkulasi otomatis berdasarkan bobot kriteria terbaru untuk periode tersebut.</div>
        </div>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><i class="fas fa-calendar-alt" style="margin-right: 5px;"></i> Periode</th>
                    <th><i class="fas fa-user-tie" style="margin-right: 5px;"></i> Karyawan</th>
                    <th><i class="fas fa-user-secret" style="margin-right: 5px;"></i> Penilai</th>
                    <th style="text-align: right;"><i class="fas fa-chart-simple" style="margin-right: 5px;"></i> Skor Akhir</th>
                    <th style="text-align: center;">Status</th>
                    <th style="text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($evaluations as $eval)
                <tr>
                    <td style="font-weight: 800; color: var(--primary);">{{ $eval->period }}</td>
                    <td style="font-weight: 700; color: #1e293b;">{{ $eval->employee->name }}</td>
                    <td style="color: var(--text-muted); font-size: 0.875rem;">{{ $eval->evaluator->name ?? 'Admin' }}</td>
                    <td style="font-weight: 800; color: var(--secondary); text-align: right; font-size: 1.1rem;">{{ number_format($eval->total_score, 4) }}</td>
                    <td style="text-align: center;">
                        @if($eval->status == 'validated')
                            <span class="badge badge-success" style="background: #dcfce7; color: #14532d; border: 1px solid #bbf7d0;"><i class="fas fa-circle-check"></i> TERVALIDASI</span>
                        @else
                            <span class="badge badge-warning" style="background: #fef9c3; color: #713f12; border: 1px solid #fde68a;"><i class="fas fa-clock"></i> PENDING</span>
                        @endif
                    </td>
                    <td style="text-align: center;">
                        <div style="display:flex; justify-content: center; gap:0.5rem">
                             @if(auth()->user()->role == 'admin' || auth()->user()->role == 'hrd')
                             <a href="{{ route('calculate', ['period' => $eval->period]) }}" class="btn" title="Lihat Laporan Ranking" style="padding: 0.4rem; background: #f8fafc; border: 1px solid #e2e8f0; color: var(--primary);"><i class="fas fa-ranking-star"></i></a>
                             @endif

                             @php 
                                $isOwner = $eval->evaluator_id == auth()->id();
                                $isAdmin = auth()->user()->role == 'admin';
                                $isHOD = auth()->user()->role == 'hod';
                                $isPending = $eval->status == 'pending';
                             @endphp

                             @if($isAdmin || ($isHOD && $isOwner && $isPending))
                             <a href="{{ route('evaluations.edit', $eval->id) }}" class="btn" title="Edit Penilaian" style="padding: 0.4rem; background: #fff7ed; border: 1px solid #ffedd5; color: #f97316;"><i class="fas fa-edit"></i></a>
                             @endif

                             @if($isAdmin || (auth()->user()->role == 'hrd' && $isPending))
                                <form action="{{ route('evaluations.validate', $eval->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn" title="Validasi Penilaian" style="padding: 0.4rem; background: #ecfdf5; border: 1px solid #10b981; color: #059669;"><i class="fas fa-check-double"></i></button>
                                </form>
                             @endif

                             @if($isAdmin || ($isHOD && $isOwner && $isPending))
                             <form action="{{ route('evaluations.destroy', $eval->id) }}" method="POST" onsubmit="return confirm('Hapus data penilaian ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn" title="Hapus" style="padding: 0.4rem; background: #fff1f2; border: 1px solid #fecaca; color: #e11d48;"><i class="fas fa-trash-can"></i></button>
                             </form>
                             @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 5rem; color: #94a3b8;">
                        <i class="fas fa-folder-open" style="font-size: 3rem; display: block; margin-bottom: 1rem; opacity: 0.2;"></i>
                        Belum ada data penilaian.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="card" style="background: #fcfaff; border: 1px dashed #c084fc;">
    <div style="display: flex; gap: 1rem; align-items: start;">
        <div style="background: #f5f3ff; color: #8b5cf6; width: 32px; height: 32px; border-radius: 8px; display:flex; align-items:center; justify-content:center; flex-shrink: 0;">
            <i class="fas fa-lightbulb"></i>
        </div>
        <p style="font-size: 0.8125rem; color: #6b21a8; margin: 0; line-height: 1.6;">
            <strong>Saran Sistem:</strong> Ingat untuk selalu menekan tombol <strong>Hitung SAW</strong> setelah menginput data baru agar peringkat karyawan diperbarui secara otomatis berdasarkan bobot kriteria terbaru.
        </p>
    </div>
</div>
@endsection
