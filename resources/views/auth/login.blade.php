<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SPK SAW Employee</title>
    <!-- Google Fonts Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --background: #f1f5f9;
        }

        body {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            font-family: 'Inter', sans-serif;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.3);
            border-radius: 20px;
            padding: 3rem;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            text-align: center;
        }

        .login-card h1 { font-weight: 700; color: #1e293b; margin-bottom: 0.5rem; font-size: 1.75rem; }
        .login-card p { color: #64748b; margin-bottom: 2rem; }

        .form-group { text-align: left; margin-bottom: 1.5rem; }
        .form-group label { display: block; font-weight: 600; margin-bottom: 0.5rem; color: #334155; }
        .form-group input {
            width: 100%;
            padding: 0.875rem;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            box-sizing: border-box;
            background: white;
            transition: 0.3s;
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
        }

        .btn-login {
            background: var(--primary);
            color: white;
            padding: 0.875rem;
            width: 100%;
            border-radius: 10px;
            border: none;
            font-weight: 700;
            cursor: pointer;
            transition: 0.3s;
            font-size: 1rem;
        }

        .btn-login:hover { background: var(--primary-hover); }

        .error-alert {
            background: #fee2e2;
            color: #b91c1c;
            padding: 0.75rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-size: 0.875rem;
        }

        .password-wrapper {
            position: relative;
        }

        .password-wrapper input {
            padding-right: 2.75rem;
        }

        .toggle-password {
            position: absolute;
            right: 0.875rem;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #94a3b8;
            transition: 0.2s;
        }

        .toggle-password:hover {
            color: var(--primary);
        }
    </style>
</head>
<body>
    <div class="login-card">
        <i class="fas fa-chart-line" style="font-size: 3rem; color: var(--primary); margin-bottom: 1rem;"></i>
        <h1>Sistem SPK-SAW</h1>
        <p>Aplikasi Penilaian Kinerja Karyawan</p>

        @if($errors->any())
            <div class="error-alert">
                Email atau password salah!
            </div>
        @endif

        <form action="/login" method="POST">
            @csrf
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="admin@example.com" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <div class="password-wrapper">
                    <input type="password" name="password" id="password" placeholder="••••••••" required>
                    <i class="fas fa-eye toggle-password" id="togglePassword"></i>
                </div>
            </div>
            <button type="submit" class="btn-login">Sign In <i class="fas fa-arrow-right"></i></button>
        </form>

        <p style="margin-top: 2rem; font-size: 0.75rem; color: #94a3b8;">
            &copy; 2026 SPK SAW System. All rights Reserved.
        </p>
    </div>

    <script>
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');

        togglePassword.addEventListener('click', function () {
            const isPassword = passwordInput.getAttribute('type') === 'password';
            passwordInput.setAttribute('type', isPassword ? 'text' : 'password');

            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    </script>
</body>
</html>