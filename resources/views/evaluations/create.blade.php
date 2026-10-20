@extends('layouts.app')

@section('title', 'Input Penilaian Baru')

@section('content')
<div style="max-width: 750px; margin: 0 auto; padding-bottom: 3rem;">
    <div class="card" style="padding: 3rem; border-radius: 24px; box-shadow: 0 20px 50px -15px rgba(0,0,0,0.1);">
        <div style="margin-bottom: 3rem; text-align: center;">
            <div style="width: 60px; height: 60px; background: #eef2ff; color: var(--primary); border-radius: 18px; display: flex; align-items: center; justify-content:center; margin: 0 auto 1.5rem auto; font-size: 1.5rem;">
                <i class="fas fa-file-signature"></i>
            </div>
            <h2 style="font-weight: 800; color: #1e293b; margin: 0; font-size: 1.75rem;">Form Penilaian Staf</h2>
            <p style="color: #64748b; margin-top: 0.5rem; font-size: 0.9375rem;">Input skor parameter kerja untuk diproses oleh algoritma SAW.</p>
        </div>

        <form action="{{ route('evaluations.store') }}" method="POST">
            @csrf
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2.5rem; padding: 2rem; background: #f8fafc; border-radius: 16px; border: 1px solid #f1f5f9;">
                <div>
                    <label style="display: block; font-weight: 700; margin-bottom: 0.625rem; color: #475569; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.025em;">Karyawan Terpilih</label>
                    <select name="employee_id" required style="width: 100%; padding: 0.875rem; border-radius: 10px; border: 1px solid #e2e8f0; outline: none; background: white; font-weight: 500; font-family: inherit;">
                        <option value="">-- Pilih Nama Staf --</option>
                        @foreach($employees as $e)
                            <option value="{{ $e->id }}">{{ $e->nip }} - {{ $e->name }} ({{ $e->department->name ?? 'Unit Umum' }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="display: block; font-weight: 700; margin-bottom: 0.625rem; color: #475569; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.025em;">Periode Laporan</label>
                    <input type="month" name="period" value="{{ date('Y-m') }}" required style="width: 100%; padding: 0.875rem; border-radius: 10px; border: 1px solid #e2e8f0; outline: none; font-weight: 500; font-family: inherit;">
                </div>
            </div>

            <div style="margin-bottom: 3rem;">
                <h3 style="font-size: 0.875rem; font-weight: 800; margin-bottom: 2rem; color: #1e293b; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 2px solid #f1f5f9; padding-bottom: 0.75rem; display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-list-check" style="color: var(--primary);"></i> Indikator Penilaian (0 - 100)
                </h3>
                
                @foreach($criteria as $c)
                <div style="margin-bottom: 1.75rem; padding: 1.25rem; border: 1px solid #f1f5f9; border-radius: 12px; transition: all 0.2s ease;" onmouseover="this.style.borderColor='var(--primary)'; this.style.background='#fbfcfe';" onmouseout="this.style.borderColor='#f1f5f9'; this.style.background='transparent';">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                        <label style="font-weight: 700; color: #334155; font-size: 0.9375rem;">{{ $c->name }} <span style="font-weight: 400; color: #94a3b8; font-size: 0.75rem; margin-left: 5px;">({{ strtoupper($c->type) }})</span></label>
                        <span class="badge" style="background: #eef2ff; color: var(--primary); font-weight: 800;">Bobot: {{ intval($c->weight * 100) }}%</span>
                    </div>
                    <div style="position: relative;">
                        <input type="number" name="scores[{{ $c->id }}]" min="0" max="100" required placeholder="Masukkan skor..." style="width: 100%; padding: 1rem 1rem 1rem 3.5rem; border-radius: 10px; border: 1px solid #e2e8f0; outline: none; font-size: 1.125rem; font-weight: 700; color: #1e293b; background: white;">
                        <div style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #cbd5e1; font-size: 1.25rem;">
                            <i class="fas fa-star-half-stroke"></i>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div style="display: flex; gap: 1.25rem; justify-content: center; padding-top: 1rem;">
                <a href="{{ route('evaluations.index') }}" class="btn" style="background: #f1f5f9; color: #475569; padding: 1rem 2.5rem; font-weight: 600;">Kembali</a>
                <button type="submit" class="btn btn-primary" style="padding: 1rem 3.5rem; font-size: 1rem; box-shadow: 0 10px 25px -5px rgba(99, 102, 241, 0.4);">Proses Deskripsi & Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
