@extends('layouts.app')

@section('title', 'Data Karyawan')

@section('content')
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2.5rem;">
        <div>
            <h2 style="font-weight: 800; color: var(--text-main); margin: 0; font-size: 1.25rem;">👥 Manajemen Karyawan</h2>
            <p style="color: var(--text-muted); margin: 0.25rem 0 0 0; font-size: 0.8125rem;">Kelola profil data staf dan penempatan departemen.</p>
        </div>
        <button onclick="document.getElementById('addModal').style.display='block'" class="btn btn-primary" style="box-shadow: 0 4px 12px rgba(99, 102, 241, 0.2);"><i class="fas fa-plus"></i> Tambah Karyawan</button>
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
                    <th style="width: 140px;">NIP</th>
                    <th>Nama Lengkap</th>
                    <th>Departemen</th>
                    <th>Jabatan / Posisi</th>
                    <th style="text-align: center;">Status</th>
                    <th style="text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($employees as $e)
                <tr>
                    <td style="font-weight: 800; color: var(--text-muted);">{{ $e->nip }}</td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <div style="width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, #f1f5f9, #e2e8f0); display:flex; align-items:center; justify-content:center; color:#94a3b8; font-weight:bold; font-size: 0.75rem;">
                                {{ substr($e->name, 0, 1) }}
                            </div>
                            <span style="font-weight: 700; color: #1e293b;">{{ $e->name }}</span>
                        </div>
                    </td>
                    <td><span class="badge" style="background:#eef2ff; color:#4338ca; border: 1px solid #e0e7ff;">{{ $e->department->name ?? 'Unassigned' }}</span></td>
                    <td><span style="color: #64748b; font-weight: 500;">{{ $e->position ?? 'Staff' }}</span></td>
                    <td style="text-align: center;"><span class="badge badge-success">Aktif</span></td>
                    <td style="text-align: center;">
                        <div style="display:flex; justify-content: center; gap:0.5rem">
                            <button onclick="editEmployee({{ $e->id }}, '{{ $e->name }}', {{ $e->department_id ?? 'null' }}, '{{ $e->position ?? 'Staff' }}')" class="btn" style="padding: 0.4rem; background: #f8fafc; border: 1px solid #e2e8f0; color: var(--primary);"><i class="fas fa-pen-to-square"></i></button>
                            <form action="{{ route('employees.destroy', $e->id) }}" method="POST" onsubmit="return confirm('Hapus karyawan ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn" style="padding: 0.4rem; background: #fff1f2; border: 1px solid #fecaca; color: #e11d48;"><i class="fas fa-trash-can"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center; padding: 4rem; color:#94a3b8">
                        <i class="fas fa-users-slash" style="font-size: 3rem; display: block; margin-bottom: 1rem; opacity: 0.2;"></i>
                        Belum ada data karyawan terdaftar.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Create -->
<div id="addModal" style="display:none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); backdrop-filter: blur(4px);">
    <div style="background: white; width: 600px; margin: 5vh auto; padding: 2.5rem; border-radius: 20px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 2rem;">
            <h3 style="font-weight: 800; color:#1e293b; font-size: 1.5rem; margin:0;">Register Karyawan Baru</h3>
            <button onclick="document.getElementById('addModal').style.display='none'" style="background:none; border:none; color:#94a3b8; cursor:pointer; font-size:1.25rem;"><i class="fas fa-times"></i></button>
        </div>
        <form action="{{ route('employees.store') }}" method="POST">
            @csrf
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div style="margin-bottom: 1.25rem;">
                    <label style="display:block; margin-bottom:0.5rem; font-weight:700; color:#475569; font-size: 0.8125rem; text-transform:uppercase;">NIP / ID Karyawan</label>
                    <input type="text" name="nip" required placeholder="Contoh: E001" style="width:100%; padding:0.875rem; border:1px solid #e2e8f0; border-radius:10px; font-weight: 500;">
                </div>
                <div style="margin-bottom: 1.25rem;">
                    <label style="display:block; margin-bottom:0.5rem; font-weight:700; color:#475569; font-size: 0.8125rem; text-transform:uppercase;">Nama Lengkap</label>
                    <input type="text" name="name" required placeholder="Nama Lengkap" style="width:100%; padding:0.875rem; border:1px solid #e2e8f0; border-radius:10px; font-weight: 500;">
                </div>
            </div>
            <div style="margin-bottom: 1.25rem;">
                <label style="display:block; margin-bottom:0.5rem; font-weight:700; color:#475569; font-size: 0.8125rem; text-transform:uppercase;">Alamat Email</label>
                <input type="email" name="email" required placeholder="email@perusahaan.com" style="width:100%; padding:0.875rem; border:1px solid #e2e8f0; border-radius:10px; font-weight: 500;">
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div style="margin-bottom: 1.5rem;">
                    <label style="display:block; margin-bottom:0.5rem; font-weight:700; color:#475569; font-size: 0.8125rem; text-transform:uppercase;">Unit / Departemen</label>
                    <select name="department_id" required style="width:100%; padding:0.875rem; border:1px solid #e2e8f0; border-radius:10px; background: white; font-weight: 500;">
                        <option value="">Pilih Departemen</option>
                        @foreach($departments as $d)
                            <option value="{{ $d->id }}">{{ $d->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="margin-bottom: 1.5rem;">
                    <label style="display:block; margin-bottom:0.5rem; font-weight:700; color:#475569; font-size: 0.8125rem; text-transform:uppercase;">Jabatan / Posisi</label>
                    <input type="text" name="position" required placeholder="Contoh: Staff Marketing" style="width:100%; padding:0.875rem; border:1px solid #e2e8f0; border-radius:10px; font-weight: 500;">
                </div>
            </div>
            <div style="display:flex; justify-content:flex-end; gap:1rem; margin-top: 1rem; padding-top: 1.5rem; border-top: 1px solid #f1f5f9;">
                <button type="button" onclick="document.getElementById('addModal').style.display='none'" class="btn" style="background:#f1f5f9; color:#475569; padding: 0.75rem 1.5rem;">Batal</button>
                <button type="submit" class="btn btn-primary" style="padding: 0.75rem 2.5rem;">Daftarkan Karyawan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit -->
<div id="editModal" style="display:none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); backdrop-filter: blur(4px);">
    <div style="background: white; width: 500px; margin: 10% auto; padding: 2.5rem; border-radius: 20px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 2rem;">
            <h3 style="font-weight: 800; color:#1e293b; font-size: 1.25rem; margin:0;">Edit Profil Karyawan</h3>
            <button onclick="document.getElementById('editModal').style.display='none'" style="background:none; border:none; color:#94a3b8; cursor:pointer;"><i class="fas fa-times"></i></button>
        </div>
        <form id="editForm" method="POST">
            @csrf @method('PUT')
            <div style="margin-bottom: 1.25rem;">
                <label style="display:block; margin-bottom:0.5rem; font-weight:700; color:#475569; font-size: 0.75rem; text-transform:uppercase;">Nama Lengkap</label>
                <input type="text" id="edit_name" name="name" required style="width:100%; padding:0.75rem; border:1px solid #e2e8f0; border-radius:8px; font-weight: 500;">
            </div>
            <div style="margin-bottom: 1.25rem;">
                <label style="display:block; margin-bottom:0.5rem; font-weight:700; color:#475569; font-size: 0.75rem; text-transform:uppercase;">Departemen</label>
                <select id="edit_department" name="department_id" required style="width:100%; padding:0.75rem; border:1px solid #e2e8f0; border-radius:8px; background:white; font-weight: 500;">
                    @foreach($departments as $d)
                        <option value="{{ $d->id }}">{{ $d->name }}</option>
                    @endforeach
                </select>
            </div>
            <div style="margin-bottom: 2rem;">
                <label style="display:block; margin-bottom:0.5rem; font-weight:700; color:#475569; font-size: 0.75rem; text-transform:uppercase;">Posisi / Jabatan</label>
                <input type="text" id="edit_position" name="position" required style="width:100%; padding:0.75rem; border:1px solid #e2e8f0; border-radius:8px; font-weight: 500;">
            </div>
            <div style="display:flex; justify-content:flex-end; gap:0.5rem; padding-top: 1.5rem; border-top: 1px solid #f1f5f9;">
                <button type="button" onclick="document.getElementById('editModal').style.display='none'" class="btn" style="background:#f1f5f9; color:#475569;">Batal</button>
                <button type="submit" class="btn btn-primary" style="padding: 0.75rem 2rem;">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function editEmployee(id, name, dept_id, position) {
        document.getElementById('editForm').action = '/employees/' + id;
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_department').value = dept_id;
        document.getElementById('edit_position').value = position;
        document.getElementById('editModal').style.display = 'block';
    }
</script>
@endsection
