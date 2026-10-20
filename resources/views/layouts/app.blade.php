<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'SPK SAW') }} - Employee Evaluation</title>
    <!-- Google Fonts Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <h2><i class="fas fa-chart-line"></i> SPK-SAW</h2>
            <nav>
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->is('dashboard*') ? 'active' : '' }}">
                    <i class="fas fa-home"></i> Dashboard
                </a>

                @if(auth()->user()->role == 'admin' || auth()->user()->role == 'hrd')
                <div class="sidebar-label" style="font-size: 0.65rem; text-transform: uppercase; color: rgba(255,255,255,0.4); margin: 1.5rem 0 0.5rem 1.5rem; font-weight: 800; letter-spacing: 0.05em;">Master Data</div>
                <a href="{{ route('employees.index') }}" class="nav-link {{ request()->is('employees*') ? 'active' : '' }}">
                    <i class="fas fa-users"></i> Data Karyawan
                </a>
                <a href="{{ route('departments.index') }}" class="nav-link {{ request()->is('departments*') ? 'active' : '' }}">
                    <i class="fas fa-building"></i> Departemen
                </a>
                <a href="{{ route('hods.index') }}" class="nav-link {{ request()->is('hods*') ? 'active' : '' }}">
                    <i class="fas fa-user-tie"></i> Manajemen HOD
                </a>
                <a href="{{ route('criteria.index') }}" class="nav-link {{ request()->is('criteria*') ? 'active' : '' }}">
                    <i class="fas fa-list-check"></i> Kriteria & Bobot
                </a>
                @endif

                <div class="sidebar-label" style="font-size: 0.65rem; text-transform: uppercase; color: rgba(255,255,255,0.4); margin: 1.5rem 0 0.5rem 1.5rem; font-weight: 800; letter-spacing: 0.05em;">Proses SAW</div>
                
                @if(auth()->user()->role == 'admin' || auth()->user()->role == 'hod')
                <a href="{{ route('evaluations.create') }}" class="nav-link {{ request()->is('evaluations/create') ? 'active' : '' }}">
                    <i class="fas fa-pen-to-square"></i> Beri Penilaian
                </a>
                @endif

                @if(auth()->user()->role == 'admin' || auth()->user()->role == 'hrd')
                <a href="{{ route('calculate', ['period' => date('Y-m')]) }}" class="nav-link {{ request()->is('calculate*') ? 'active' : '' }}">
                    <i class="fas fa-trophy"></i> Laporan & Ranking
                </a>
                @endif

                <a href="{{ route('evaluations.index') }}" class="nav-link {{ request()->is('evaluations') ? 'active' : '' }}">
                    <i class="fas fa-history"></i> Riwayat Penilaian
                </a>

                <div style="margin: 1.5rem 0; border-top: 1px solid rgba(255,255,255,0.1);"></div>
                <a href="{{ route('profile.index') }}" class="nav-link {{ request()->is('profile*') ? 'active' : '' }}">
                    <i class="fas fa-user-circle"></i> Profil Saya
                </a>
                <a href="{{ route('logout') }}" class="nav-link" style="color: #fda4af;">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <style>
                .profile-header-link {
                    display: flex; 
                    align-items: center; 
                    gap: 1rem; 
                    background: white; 
                    padding: 0.5rem 1rem; 
                    border-radius: 99px; 
                    box-shadow: var(--shadow-sm);
                    text-decoration: none;
                    color: inherit;
                    transition: all 0.2s;
                }
                .profile-header-link:hover {
                    box-shadow: var(--shadow-md);
                    transform: translateY(-1px);
                }
            </style>
            <header>
                <div>
                    <h1>@yield('title', 'Dashboard')</h1>
                    <p style="color: var(--text-muted); margin: 0.25rem 0 0 0; font-size: 0.875rem;">Sistem Pendukung Keputusan Metode SAW</p>
                </div>
                <a href="{{ route('profile.index') }}" class="profile-header-link">
                    <div style="text-align: right;">
                        <div style="font-weight: 700; font-size: 0.875rem;">{{ auth()->user()->name ?? 'System Admin' }}</div>
                        <div style="font-size: 0.75rem; color: var(--text-muted);">{{ ucfirst(auth()->user()->role ?? 'Admin') }}</div>
                    </div>
                    <div style="width: 36px; height: 36px; border-radius: 50%; background: linear-gradient(135deg, var(--primary), var(--secondary)); display: flex; align-items:center; justify-content:center; color:white; font-weight:bold;">
                        {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
                    </div>
                </a>
            </header>

            <div class="page-content">
                @yield('content')
            </div>
        </main>
    </div>

    @stack('scripts')
</body>
</html>
