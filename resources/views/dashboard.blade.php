@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="stat-grid">
    <div class="stat-card" style="border-left-color: var(--primary);">
        <div style="display: flex; justify-content: space-between; align-items: start;">
            <div>
                <h3>Total Karyawan</h3>
                <div class="value">{{ $totalEmployees ?? 0 }}</div>
            </div>
            <div style="width: 48px; height: 48px; background: #eef2ff; color: var(--primary); border-radius: 12px; display:flex; align-items:center; justify-content:center; font-size: 1.25rem;">
                <i class="fas fa-users-viewfinder"></i>
            </div>
        </div>
    </div>
    <div class="stat-card" style="border-left-color: var(--secondary);">
        <div style="display: flex; justify-content: space-between; align-items: start;">
            <div>
                <h3>Departemen</h3>
                <div class="value">{{ $totalDepts ?? 0 }}</div>
            </div>
            <div style="width: 48px; height: 48px; background: #f5f3ff; color: var(--secondary); border-radius: 12px; display:flex; align-items:center; justify-content:center; font-size: 1.25rem;">
                <i class="fas fa-building-user"></i>
            </div>
        </div>
    </div>
    <div class="stat-card" style="border-left-color: var(--accent);">
        <div style="display: flex; justify-content: space-between; align-items: start;">
            <div>
                <h3>Kriteria SAW</h3>
                <div class="value">{{ $totalCriteria ?? 0 }}</div>
            </div>
            <div style="width: 48px; height: 48px; background: #fdf2f8; color: var(--accent); border-radius: 12px; display:flex; align-items:center; justify-content:center; font-size: 1.25rem;">
                <i class="fas fa-list-check"></i>
            </div>
        </div>
    </div>
    <div class="stat-card" style="border-left-color: #f59e0b;">
        <div style="display: flex; justify-content: space-between; align-items: start;">
            <div>
                <h3>Periode Aktif</h3>
                <div class="value">{{ date('M Y') }}</div>
            </div>
            <div style="width: 48px; height: 48px; background: #fffbeb; color: #f59e0b; border-radius: 12px; display:flex; align-items:center; justify-content:center; font-size: 1.25rem;">
                <i class="fas fa-calendar-check"></i>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h2 style="font-weight: 800; color: var(--text-main); margin: 0; font-size: 1.25rem;">🏆 Peringkat Karyawan Terbaik</h2>
            <p style="color: var(--text-muted); margin: 0.25rem 0 0 0; font-size: 0.8125rem;">Berdasarkan perhitungan SPK-SAW periode saat ini.</p>
        </div>
        <a href="{{ route('calculate', ['period' => date('Y-m')]) }}" class="btn btn-primary"><i class="fas fa-calculator"></i> Perbarui Hasil</a>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th style="width: 80px;">Rank</th>
                    <th>NIP</th>
                    <th>Nama Karyawan</th>
                    <th>Departemen</th>
                    <th style="text-align: right;">Skor Akhir (V)</th>
                    <th style="text-align: center;">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rankings as $rank)
                <tr>
                    <td>
                        <div style="width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: 800; 
                            {{ $loop->index == 0 ? 'background: #f59e0b; color: white;' : ($loop->index == 1 ? 'background: #94a3b8; color: white;' : ($loop->index == 2 ? 'background: #b45309; color: white;' : 'background: #f1f5f9; color: #64748b;')) }}">
                            {{ $rank->rank }}
                        </div>
                    </td>
                    <td style="font-weight: 700; color: #64748b;">{{ $rank->employee->nip ?? '-' }}</td>
                    <td style="font-weight: 700; color: #1e293b;">{{ $rank->employee->name }}</td>
                    <td><span class="badge" style="background:#e0e7ff; color:#3730a3">{{ $rank->employee->department->name ?? '-' }}</span></td>
                    <td style="font-weight: 800; color: var(--primary); text-align: right; font-size: 1.1rem;">{{ number_format($rank->total_score, 4) }}</td>
                    <td style="text-align: center;"><span class="badge badge-success">Terhitung</span></td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 4rem; color: #94a3b8;">
                        <i class="fas fa-inbox" style="font-size: 3rem; display: block; margin-bottom: 1rem; opacity: 0.3;"></i>
                        Belum ada data penilaian untuk periode ini.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
