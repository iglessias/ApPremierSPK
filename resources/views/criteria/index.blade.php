@extends('layouts.app')

@section('title', 'Manajemen Kriteria')

@section('content')
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2.5rem;">
        <div>
            <h2 style="font-weight: 800; color: var(--text-main); margin: 0; font-size: 1.25rem;">⚖️ Kriteria Penilaian</h2>
            <p style="color: var(--text-muted); margin: 0.25rem 0 0 0; font-size: 0.8125rem;">Atur parameter ukur dan bobot preferensi penentuan peringkat.</p>
        </div>
        <button onclick="document.getElementById('addModal').style.display='block'" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Kriteria</button>
    </div>

    @php $totalWeight = \App\Models\Criterion::sum('weight'); @endphp
    <div style="margin-bottom: 2rem; padding: 1.25rem; border-radius: 12px; @if($totalWeight != 1) background: #fffbeb; border: 1px solid #fde68a; color: #92400e; @else background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; @endif display:flex; align-items:center; gap:15px; font-weight: 600;">
        <div style="width: 44px; height: 44px; border-radius: 50%; display:flex; align-items:center; justify-content:center; background: white; font-size: 1.1rem; box-shadow: var(--shadow-sm);">
             <i class="fas @if($totalWeight != 1) fa-scale-unbalanced @else fa-scale-balanced @endif"></i>
        </div>
        <div>
            <div style="font-size: 1rem;">Total Bobot Akumulasi: <span style="font-weight: 800;">{{ $totalWeight * 100 }}%</span></div>
            <div style="font-weight: 400; font-size: 0.75rem; opacity: 0.8;">
                @if($totalWeight != 1) 
                    Harap sesuaikan bobot agar total berjumlah 1.0 (100%) untuk akurasi SAW.
                @else
                    Konfigurasi bobot sudah ideal dan siap untuk kalkulasi.
                @endif
            </div>
        </div>
    </div>

    @if(session('success'))
        <div style="background: #f0fdf4; color: #166534; padding: 1rem 1.5rem; border-radius: 10px; margin-bottom: 2rem; border: 1px solid #bbf7d0; font-weight: 600;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th style="width: 100px;">Kode</th>
                    <th>Nama Kriteria</th>
                    <th>Bobot Kepentingan</th>
                    <th style="text-align: center;">Tipe</th>
                    <th style="text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($criteria as $c)
                <tr>
                    <td><span style="font-weight: 800; color: var(--primary); background: #eef2ff; padding: 0.25rem 0.6rem; border-radius: 6px; border: 1px solid #e0e7ff;">{{ $c->code }}</span></td>
                    <td style="font-weight: 700; color: #1e293b;">{{ $c->name }}</td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <div style="flex: 1; max-width: 120px; height: 8px; background: #f1f5f9; border-radius: 4px; overflow: hidden; border: 1px solid #e2e8f0;">
                                <div style="width: {{ $c->weight * 100 }}%; height: 100%; background: linear-gradient(to right, var(--primary), var(--secondary)); border-radius: 4px;"></div>
                            </div>
                            <span style="font-weight: 800; color: #475569; font-size: 0.9rem;">{{ number_format($c->weight, 2) }}</span>
                        </div>
                    </td>
                    <td style="text-align: center;">
                        <span class="badge {{ $c->type == 'benefit' ? 'badge-success' : 'badge-warning' }}" style="padding: 0.4rem 1rem; text-transform: uppercase; font-size: 0.65rem; min-width: 90px; justify-content:center;">
                            {{ $c->type }}
                        </span>
                    </td>
                    <td style="text-align: center;">
                        <div style="display:flex; justify-content: center; gap:0.5rem">
                            <button onclick="editCriterion({{ $c->id }}, '{{ $c->name }}', {{ $c->weight }}, '{{ $c->type }}')" class="btn" style="padding: 0.4rem; background: #f8fafc; border: 1px solid #e2e8f0; color: var(--primary);"><i class="fas fa-edit"></i></button>
                            <form action="{{ route('criteria.destroy', $c->id) }}" method="POST" onsubmit="return confirm('Hapus kriteria ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn" style="padding: 0.4rem; background: #fff1f2; border: 1px solid #fecaca; color: #e11d48;"><i class="fas fa-trash-can"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align:center; padding: 4rem; color:#94a3b8">
                        <i class="fas fa-layer-group" style="font-size: 3rem; display: block; margin-bottom: 1rem; opacity: 0.2;"></i>
                        Belum ada parameter kriteria.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Create -->
<div id="addModal" style="display:none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); backdrop-filter: blur(4px);">
    <div style="background: white; width: 500px; margin: 10vh auto; padding: 2.5rem; border-radius: 20px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);">
        <h3 style="margin-bottom: 2rem; font-weight: 800; color:#1e293b; font-size: 1.5rem;">Tambah Kriteria Baru</h3>
        <form action="{{ route('criteria.store') }}" method="POST">
            @csrf
            <div style="margin-bottom: 1.25rem;">
                <label style="display:block; margin-bottom:0.5rem; font-weight:700; color:#475569; font-size:0.75rem; text-transform:uppercase;">Kode (Contoh: C1)</label>
                <input type="text" name="code" required placeholder="C1" style="width:100%; padding:0.875rem; border:1px solid #e2e8f0; border-radius:10px; font-weight: 500;">
            </div>
            <div style="margin-bottom: 1.25rem;">
                <label style="display:block; margin-bottom:0.5rem; font-weight:700; color:#475569; font-size:0.75rem; text-transform:uppercase;">Nama Kriteria</label>
                <input type="text" name="name" required placeholder="Contoh: Kedisiplinan" style="width:100%; padding:0.875rem; border:1px solid #e2e8f0; border-radius:10px; font-weight: 500;">
            </div>
            <div style="margin-bottom: 1.25rem;">
                <label style="display:block; margin-bottom:0.5rem; font-weight:700; color:#475569; font-size:0.75rem; text-transform:uppercase;">Bobot (0.01 - 1.00)</label>
                <input type="number" step="0.01" name="weight" required placeholder="0.25" style="width:100%; padding:0.875rem; border:1px solid #e2e8f0; border-radius:10px; font-weight: 500;">
            </div>
            <div style="margin-bottom: 2rem;">
                <label style="display:block; margin-bottom:0.5rem; font-weight:700; color:#475569; font-size:0.75rem; text-transform:uppercase;">Tipe Kriteria</label>
                <select name="type" required style="width:100%; padding:0.875rem; border:1px solid #e2e8f0; border-radius:10px; background:white; font-weight: 500;">
                    <option value="benefit">Benefit (Makin Besar Makin Baik)</option>
                    <option value="cost">Cost (Makin Kecil Makin Baik)</option>
                </select>
            </div>
            <div style="display:flex; justify-content:flex-end; gap:1rem; padding-top: 1.5rem; border-top: 1px solid #f1f5f9;">
                <button type="button" onclick="document.getElementById('addModal').style.display='none'" class="btn" style="background:#f1f5f9; color:#475569;">Batal</button>
                <button type="submit" class="btn btn-primary" style="padding: 0.75rem 2rem;">Simpan Kriteria</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit -->
<div id="editModal" style="display:none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); backdrop-filter: blur(4px);">
    <div style="background: white; width: 500px; margin: 10vh auto; padding: 2.5rem; border-radius: 20px;">
        <h3 style="margin-bottom: 2rem; font-weight: 800; color:#1e293b;">Edit Parameter Kriteria</h3>
        <form id="editForm" method="POST">
            @csrf @method('PUT')
            <div style="margin-bottom: 1.25rem;">
                <label style="display:block; margin-bottom:0.5rem; font-weight:700; font-size:0.75rem; color:#475569;">NAMA KRITERIA</label>
                <input type="text" id="edit_name" name="name" required style="width:100%; padding:0.75rem; border:1px solid #e2e8f0; border-radius:8px; font-weight: 500;">
            </div>
            <div style="margin-bottom: 1.25rem;">
                <label style="display:block; margin-bottom:0.5rem; font-weight:700; font-size:0.75rem; color:#475569;">BOBOT (DESIMAL)</label>
                <input type="number" id="edit_weight" step="0.01" name="weight" required style="width:100%; padding:0.75rem; border:1px solid #e2e8f0; border-radius:8px; font-weight: 500;">
            </div>
            <div style="margin-bottom: 2rem;">
                <label style="display:block; margin-bottom:0.5rem; font-weight:700; font-size:0.75rem; color:#475569;">TIPE</label>
                <select id="edit_type" name="type" required style="width:100%; padding:0.75rem; border:1px solid #e2e8f0; border-radius:8px; background:white; font-weight: 500;">
                    <option value="benefit">Benefit</option>
                    <option value="cost">Cost</option>
                </select>
            </div>
            <div style="display:flex; justify-content:flex-end; gap:0.5rem; padding-top: 1.5rem; border-top: 1px solid #f1f5f9;">
                <button type="button" onclick="document.getElementById('editModal').style.display='none'" class="btn" style="background:#f1f5f9;">Batal</button>
                <button type="submit" class="btn btn-primary" style="padding: 0.75rem 2rem;">Perbarui</button>
            </div>
        </form>
    </div>
</div>

<script>
    function editCriterion(id, name, weight, type) {
        document.getElementById('editForm').action = '/criteria/' + id;
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_weight').value = weight;
        document.getElementById('edit_type').value = type;
        document.getElementById('editModal').style.display = 'block';
    }
</script>
@endsection
