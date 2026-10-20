@extends('layouts.app')

@section('title', 'Manajemen Departemen')

@section('content')
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2.5rem;">
        <div>
            <h2 style="font-weight: 800; color: var(--text-main); margin: 0; font-size: 1.25rem;">🏢 Unit Kerja & Departemen</h2>
            <p style="color: var(--text-muted); margin: 0.25rem 0 0 0; font-size: 0.8125rem;">Struktur organisasi inti perusahaan sebagai dasar pengelompokan karyawan.</p>
        </div>
        <button onclick="document.getElementById('addModal').style.display='block'" class="btn btn-primary" style="box-shadow: 0 4px 12px rgba(99, 102, 241, 0.2);"><i class="fas fa-plus"></i> Tambah Departemen</button>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1.5rem;">
        @forelse($departments as $dept)
        <div class="stat-card" style="border-left: 6px solid var(--secondary); background: white; padding: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1.25rem;">
                <div style="flex: 1; padding-right: 1rem;">
                   <h3 style="font-size: 1.125rem; font-weight: 800; color: #1e293b; margin:0 0 0.5rem 0; text-transform:none; letter-spacing:normal;">{{ $dept->name }}</h3>
                   <p style="color: #64748b; font-size: 0.875rem; line-height: 1.5; margin:0;">{{ $dept->description ?? 'Unit kerja inti untuk operasional harian perusahaan.' }}</p>
                </div>
                <div style="background: #f5f3ff; width: 44px; height: 44px; display:flex; align-items:center; justify-content:center; border-radius: 12px; color: var(--secondary); flex-shrink:0;">
                    <i class="fas fa-building" style="font-size: 1.125rem;"></i>
                </div>
            </div>
            
            <div style="margin-top: 1rem; padding-top: 1.25rem; border-top: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
                <span class="badge" style="background:#f5f3ff; color:#7c3aed; font-weight:800; border: 1px solid #ddd6fe;">
                    <i class="fas fa-user-group" style="margin-right:5px;"></i> {{ $dept->employees_count ?? 0 }} Staff
                </span>
                <div style="display:flex; gap:0.5rem">
                    <button onclick="editDepartment({{ $dept->id }}, '{{ $dept->name }}', '{{ $dept->description }}')" class="btn" style="padding: 0.4rem; background: #f8fafc; border: 1px solid #e2e8f0; color: var(--primary);"><i class="fas fa-pen-to-square"></i></button>
                    <form action="{{ route('departments.destroy', $dept->id) }}" method="POST" onsubmit="return confirm('Hapus departemen ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn" style="padding: 0.4rem; background: #fff1f2; border: 1px solid #fecaca; color: #e11d48;"><i class="fas fa-trash-can"></i></button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div style="grid-column: 1 / -1; text-align:center; padding: 5rem; color:#94a3b8; background: #fafafa; border-radius: 16px; border: 2px dashed #eee;">
            <i class="fas fa-building-circle-exclamation" style="font-size: 3rem; display: block; margin-bottom: 1rem; opacity: 0.2;"></i>
            Belum ada data departemen terdaftar.
        </div>
        @endforelse
    </div>
</div>

<!-- Modal Create -->
<div id="addModal" style="display:none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); backdrop-filter: blur(4px);">
    <div style="background: white; width: 500px; margin: 10vh auto; padding: 2.5rem; border-radius: 20px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 2rem;">
            <h3 style="font-weight: 800; color:#1e293b; font-size: 1.5rem; margin:0;">Tambah Unit Kerja Baru</h3>
            <button onclick="document.getElementById('addModal').style.display='none'" style="background:none; border:none; color:#94a3b8; cursor:pointer;"><i class="fas fa-times"></i></button>
        </div>
        <form action="{{ route('departments.store') }}" method="POST">
            @csrf
            <div style="margin-bottom: 1.5rem;">
                <label style="display:block; margin-bottom:0.5rem; font-weight:700; color:#475569; font-size: 0.75rem; text-transform:uppercase;">Nama Departemen / Unit</label>
                <input type="text" name="name" required placeholder="Contoh: IT Support" style="width:100%; padding:0.875rem; border:1px solid #e2e8f0; border-radius:10px; font-weight: 500;">
            </div>
            <div style="margin-bottom: 2rem;">
                <label style="display:block; margin-bottom:0.5rem; font-weight:700; color:#475569; font-size: 0.75rem; text-transform:uppercase;">Deskripsi Singkat</label>
                <textarea name="description" placeholder="Jelaskan fungsi utama departemen ini..." style="width:100%; padding:0.875rem; border:1px solid #e2e8f0; border-radius:10px; height: 120px; resize:none; font-weight: 400;"></textarea>
            </div>
            <div style="display:flex; justify-content:flex-end; gap:1rem; padding-top: 1.5rem; border-top: 1px solid #f1f5f9;">
                <button type="button" onclick="document.getElementById('addModal').style.display='none'" class="btn" style="background:#f1f5f9; color:#475569;">Batal</button>
                <button type="submit" class="btn btn-primary" style="padding: 0.75rem 2rem;">Simpan Unit</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit -->
<div id="editModal" style="display:none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); backdrop-filter: blur(4px);">
    <div style="background: white; width: 500px; margin: 10vh auto; padding: 2.5rem; border-radius: 20px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 2rem;">
            <h3 style="font-weight: 800; color:#1e293b; font-size: 1.25rem; margin:0;">Edit Data Departemen</h3>
            <button onclick="document.getElementById('editModal').style.display='none'" style="background:none; border:none; color:#94a3b8; cursor:pointer;"><i class="fas fa-times"></i></button>
        </div>
        <form id="editForm" method="POST">
            @csrf @method('PUT')
            <div style="margin-bottom: 1.5rem;">
                <label style="display:block; margin-bottom:0.5rem; font-weight:700; color:#475569; font-size: 0.75rem; text-transform:uppercase;">Nama Departemen</label>
                <input type="text" id="edit_name" name="name" required style="width:100%; padding:0.875rem; border:1px solid #e2e8f0; border-radius:10px; font-weight: 500;">
            </div>
            <div style="margin-bottom: 2rem;">
                <label style="display:block; margin-bottom:0.5rem; font-weight:700; color:#475569; font-size: 0.75rem; text-transform:uppercase;">Deskripsi</label>
                <textarea id="edit_description" name="description" style="width:100%; padding:0.875rem; border:1px solid #e2e8f0; border-radius:10px; height: 120px; resize:none; font-weight: 400;"></textarea>
            </div>
            <div style="display:flex; justify-content:flex-end; gap:1rem; padding-top: 1.5rem; border-top: 1px solid #f1f5f9;">
                <button type="button" onclick="document.getElementById('editModal').style.display='none'" class="btn" style="background:#f1f5f9;">Batal</button>
                <button type="submit" class="btn btn-primary" style="padding: 0.75rem 2rem;">Update Unit</button>
            </div>
        </form>
    </div>
</div>

<script>
    function editDepartment(id, name, description) {
        document.getElementById('editForm').action = '/departments/' + id;
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_description').value = description;
        document.getElementById('editModal').style.display = 'block';
    }
</script>
@endsection
