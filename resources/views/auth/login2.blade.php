<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Portal Surat FST</title>
    <link href="{{ asset('build/assets') }}/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Figtree, ui-sans-serif, system-ui, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
            background: #f5f7fa;
            overflow-x: hidden;
        }

        .auth-container {
            display: flex;
            min-height: 100vh;
            align-items: center;
            justify-content: center;
            position: relative;
            /* background: linear-gradient(135deg, #e6f0fa 0%, #d1e3f6 100%); */
            background: linear-gradient(135deg, #f3f3f4 0%, #e6f0fa 100%);
            overflow: hidden;
        }

        /* Background floating circles */
        /* .auth-container::before,
        .auth-container::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            background: rgba(74, 144, 226, 0.15);
            animation: float 15s infinite ease-in-out;
        } */
        /* 
        .auth-container::before {
            width: 200px;
            height: 200px;
            top: 10%;
            left: 15%;
            opacity: 0.7;
        } */

        .auth-container::after {
            width: 150px;
            height: 150px;
            bottom: 20%;
            right: 10%;
            opacity: 0.5;
            animation-delay: 5s;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-30px);
            }
        }

        .login-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 2rem;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
            border: 1px solid transparent;
            background: linear-gradient(#ffffff, #ffffff) padding-box, linear-gradient(135deg, #4a90e2, #d1e3f6) border-box;
            animation: fadeInScale 0.8s ease-out;
        }

        @keyframes fadeInScale {
            from {
                opacity: 0;
                transform: scale(0.95);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        /* .login-card:hover {
            transform: translateY(-3px);
        } */

        .login-logo img {
            height: 70px;
            margin: 0 auto 1.2rem;
            display: block;
        }

        .login-title {
            font-size: 1.4rem;
            font-weight: 500;
            text-align: center;
            color: #1a202c;
            margin-bottom: 0.4rem;
        }

        .login-subtitle {
            font-size: 0.8rem;
            text-align: center;
            color: #718096;
            margin-bottom: 1.2rem;
        }

        .form-label {
            font-size: 0.9rem;
            font-weight: 500;
            color: #2d3748;
            margin-bottom: 0.4rem;
            display: block;
        }

        .input-icon {
            position: relative;
            margin-bottom: 1rem;
        }

        .form-input {
            width: 100%;
            padding: 0.7rem 2.2rem 0.7rem 0.9rem;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            font-size: 0.9rem;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: #4a90e2;
            box-shadow: 0 0 0 2px rgba(74, 144, 226, 0.1);
        }

        .input-icon i {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #a0aec0;
            font-size: 0.9rem;
        }

        .form-extra {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.85rem;
            margin-bottom: 1rem;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 6px;
            color: #4a5568;
        }

        .forgot-link {
            color: #4a90e2;
            text-decoration: none;
            font-weight: 500;
        }

        .forgot-link:hover {
            text-decoration: underline;
        }

        .btn-login,
        .btn-qr,
        .btn-google {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 0.7rem;
            border: none;
            border-radius: 6px;
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.2s ease, transform 0.2s ease, box-shadow 0.2s ease;
            margin-bottom: 0.8rem;
        }

        .btn-login:hover,
        .btn-qr:hover,
        .btn-google:hover {
            animation: pulse 0.3s ease;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.08);
            }

            100% {
                transform: scale(1);
            }
        }

        .btn-login {
            background: #4a90e2;
            color: #ffffff;
        }

        .btn-login:hover {
            background: #357abd;
        }

        .btn-qr {
            background: #2d3748;
            color: #ffffff;
        }

        .btn-qr:hover {
            background: #1a202c;
        }

        .btn-google {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            color: #2d3748;
            gap: 8px;
        }

        .btn-google img {
            width: 18px;
            height: 18px;
        }

        .btn-google:hover {
            background: #f7fafc;
        }

        .register-text {
            text-align: center;
            font-size: 0.85rem;
            color: #718096;
            margin-top: 1rem;
        }

        .register-text a {
            color: #4a90e2;
            text-decoration: none;
            font-weight: 500;
        }

        .register-text a:hover {
            text-decoration: underline;
        }

        .alert-success {
            background: #e6fffa;
            color: #2e7d32;
            border: 1px solid #b2f5ea;
            padding: 0.6rem;
            border-radius: 6px;
            font-size: 0.85rem;
            margin-bottom: 1rem;
            text-align: center;
        }

        .error_message {
            background: #fff5f5;
            color: #c53030;
            border: 1px solid #feb2b2;
            padding: 0.6rem;
            border-radius: 6px;
            font-size: 0.85rem;
            margin-bottom: 1rem;
            text-align: center;
            opacity: 1;
            transition: opacity 0.5s ease;
        }

        @media (max-width: 768px) {
            .login-card {
                padding: 1.5rem;
                max-width: 90%;
            }

            .login-logo img {
                height: 60px;
            }

            .auth-container::before,
            .auth-container::after {
                width: 100px;
                height: 100px;
            }
        }
    </style>
</head>

<body>
    <div class="auth-container">
        <!-- Form Login -->
        <div class="login-card">
            <div class="login-logo">
                <img src="{{ asset('logo/fst.png') }}" alt="Logo">
            </div>

            <p class="login-title">Selamat Datang di Portal Surat FST</p>
            <p class="login-subtitle">Masuk untuk memulai sesi Anda</p>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <label for="email" class="form-label">Email</label>
                <div class="input-icon">
                    <input id="email" class="form-input" type="email" name="email" value="{{old('email')}}" required autofocus autocomplete="username" />
                    <small class="text-danger" id="email_error">@if($errors->has('email')) {{ $errors->first('email') }} @endif</small>
                    <i class="fas fa-envelope icon"></i>
                </div>

                <label for="password" class="form-label">Password</label>
                <div class="input-icon">
                    <input id="password" class="form-input" type="password" name="password" required autocomplete="current-password" />
                    <small class="text-danger" id="email_error">@if($errors->has('password')) {{ $errors->first('password') }} @endif</small>
                    <i class="fas fa-lock icon"></i>
                </div>

                <div class="form-extra">
                    <label class="remember-me">
                        <input type="checkbox" name="remember"> Ingat saya
                    </label>

                    @if (Route::has('password.request'))
                    <a class="forgot-link" href="{{ route('password.request') }}">Lupa password?</a>
                    @endif
                </div>

                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt mr-2"></i> Masuk
                </button>

                <a href="#" class="btn-qr">
                    <i class="fas fa-qrcode mr-2"></i> Tracking Surat
                </a>

                <a href="#" class="btn-google">
                    <img src="https://www.gstatic.com/marketing-cms/assets/images/d5/dc/cfe9ce8b4425b410b49b7f2dd3f3/g.webp=s96-fcrop64=1,00000000ffffffff-rw" alt="Google Logo">
                    Login dengan Google
                </a>

                @if (Route::has('register'))
                <p class="register-text">
                    Belum punya akun? <a href="{{ route('register') }}">Daftar</a>
                </p>
                @endif
            </form>
        </div>
    </div>
</body>

</html>