@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
<div style="max-width: 800px; margin: 0 auto; padding-bottom: 3rem;">
    <div class="card" style="padding: 0; overflow: hidden; border-radius: 20px;">
        <div style="height: 140px; background: linear-gradient(135deg, var(--primary), var(--secondary)); position: relative;">
            <div style="position: absolute; bottom: -45px; left: 40px; width: 90px; height: 90px; border-radius: 24px; background: white; border: 4px solid white; box-shadow: 0 10px 25px rgba(0,0,0,0.1); display: flex; align-items: center; justify-content: center; font-size: 2.5rem; color: var(--primary); font-weight: 800;">
                {{ substr($user->name, 0, 1) }}
            </div>
        </div>
        
        <div style="padding: 70px 40px 40px 40px;">
            <div style="margin-bottom: 2.5rem;">
                <h2 style="font-weight: 800; color: #1e293b; margin: 0; font-size: 1.75rem;">{{ $user->name }}</h2>
                <div style="display: flex; align-items: center; gap: 10px; margin-top: 0.5rem; color: #64748b; font-weight: 500;">
                    <i class="fas fa-id-badge"></i> 
                    <span style="text-transform: uppercase; font-size: 0.75rem; background: #f1f5f9; padding: 0.25rem 0.75rem; border-radius: 100px; color: #475569;">{{ $user->role ?? 'User' }}</span>
                    <span>•</span>
                    <span style="font-size: 0.875rem;">{{ $user->email }}</span>
                </div>
            </div>

            @if(session('success'))
                <div style="background: #f0fdf4; color: #166534; padding: 1rem 1.5rem; border-radius: 12px; margin-bottom: 2.5rem; border: 1px solid #bbf7d0; font-weight: 600; display: flex; align-items: center; gap: 12px;">
                    <i class="fas fa-check-circle" style="font-size: 1.25rem;"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div style="background: #fff1f2; color: #9f1239; padding: 1rem 1.5rem; border-radius: 12px; margin-bottom: 2.5rem; border: 1px solid #fecaca; font-weight: 500;">
                    <ul style="margin: 0; padding-left: 1.25rem;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('profile.update') }}" method="POST">
                @csrf
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                    <div>
                        <label style="display: block; font-weight: 700; margin-bottom: 0.75rem; color: #475569; font-size: 0.75rem; text-transform: uppercase;">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required style="width: 100%; padding: 0.875rem; border-radius: 10px; border: 1px solid #e2e8f0; outline: none; font-weight: 500;">
                    </div>
                    <div>
                        <label style="display: block; font-weight: 700; margin-bottom: 0.75rem; color: #475569; font-size: 0.75rem; text-transform: uppercase;">Email Akun</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required style="width: 100%; padding: 0.875rem; border-radius: 10px; border: 1px solid #e2e8f0; outline: none; font-weight: 500;">
                    </div>
                </div>

                <div style="background: #f8fafc; padding: 2rem; border-radius: 16px; border: 1px solid #f1f5f9; margin-bottom: 2.5rem;">
                    <h3 style="font-size: 0.875rem; font-weight: 800; margin-bottom: 1.5rem; color: #1e293b; text-transform: uppercase; letter-spacing: 0.05em; display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-shield-alt" style="color: var(--primary);"></i> Ganti Kata Sandi
                    </h3>
                    <p style="color: #64748b; font-size: 0.8125rem; margin-bottom: 1.5rem;">Kosongkan jika tidak ingin mengubah kata sandi.</p>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                        <div>
                            <label style="display: block; font-weight: 700; margin-bottom: 0.625rem; color: #475569; font-size: 0.75rem; text-transform: uppercase;">Sandi Baru</label>
                            <input type="password" name="password" placeholder="Min. 6 Karakter" style="width: 100%; padding: 0.875rem; border-radius: 10px; border: 1px solid #e2e8f0; outline: none; background: white;">
                        </div>
                        <div>
                            <label style="display: block; font-weight: 700; margin-bottom: 0.625rem; color: #475569; font-size: 0.75rem; text-transform: uppercase;">Konfirmasi Sandi Baru</label>
                            <input type="password" name="password_confirmation" placeholder="Ulangi Sandi" style="width: 100%; padding: 0.875rem; border-radius: 10px; border: 1px solid #e2e8f0; outline: none; background: white;">
                        </div>
                    </div>
                </div>

                <div style="display: flex; gap: 1.25rem;">
                    <button type="submit" class="btn btn-primary" style="padding: 1rem 3rem; font-size: 1rem; box-shadow: 0 10px 20px -5px rgba(99, 102, 241, 0.4);">Simpan Perubahan Profil</button>
                    <a href="{{ route('dashboard') }}" class="btn" style="background: #f1f5f9; color: #475569; padding: 1rem 2.5rem; font-weight: 600;">Kembali ke Dashboard</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
