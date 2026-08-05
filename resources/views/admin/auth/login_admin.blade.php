<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login - Sistem Manajemen Surat</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa;
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            display: flex;
            align-items: center;
            min-height: 100vh;
        }

        .login-container {
            max-width: 400px;
            margin: 0 auto;
            width: 100%;
        }

        .login-card {
            background-color: #fff;
            border-radius: 9px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 2rem;
        }

        .login-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            text-align: center;
            color: #333;
        }

        .form-control {
            padding: 0.75rem 1rem;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .form-control:focus {
            border-color: #4e73df;
            box-shadow: 0 0 0 0.25rem rgba(78, 115, 223, 0.25);
        }

        .btn-login {
            background-color: #4e73df;
            border: none;
            color: white;
            padding: 0.75rem 1rem;
            border-radius: 4px;
            width: 100%;
            font-weight: 500;
        }

        .btn-login:hover {
            background-color: #3a56b0;
        }

        .alert {
            padding: 0.75rem 1rem;
            margin-bottom: 1rem;
            border-radius: 4px;
            border: none;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #842029;
        }

        .form-check-input:checked {
            background-color: #4e73df;
            border-color: #4e73df;
        }

        .form-label {
            font-weight: 500;
            color: #555;
        }

        .login-footer {
            margin-top: 1.5rem;
            text-align: center;
            font-size: 0.875rem;
            color: #6c757d;
        }

        .login-links {
            margin-top: 1rem;
            text-align: center;
            font-size: 0.875rem;
        }

        .login-links a {
            color: #4e73df;
            text-decoration: none;
        }

        .login-links a:hover {
            text-decoration: underline;
        }

        .logo{
            display: flex;
            justify-content: center;
            align-items: center; 
            flex-direction: column;
        }
    </style>
</head>
<body>
    <div class="container login-container">
        <div class="login-card">
            <div class="logo">
                <img src="{{ asset('logo_lab.png') }}" alt="logo" >
                <h1 class="login-title">Login</h1>
            </div>

            @if (session('loginError'))
            <div class="alert alert-danger" role="alert">
                {{ session('loginError') }}
            </div>
            @endif

            @if ($errors->has('username') || $errors->has('password'))
            <div class="alert alert-danger" role="alert">
                Username dan Password tidak boleh kosong.
            </div>
            @endif

            <form action="/login" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" placeholder="Masukkan username">
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan password">
                </div>

                {{-- <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="customCheck" name="remember">
                    <label class="form-check-label" for="customCheck">Remember Me</label>
                </div> --}}

                <button type="submit" class="btn btn-login">Login</button>
            </form>
{{-- 
            <div class="login-links">
                <a href="forgot-password.html">Lupa Password?</a> |
                <a href="register.html">Daftar Akun</a>
            </div> --}}

            <div class="login-footer">
                &copy; {{ date('Y') }} Sistem Manajemen Surat LAB ICT
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
