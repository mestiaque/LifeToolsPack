<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'ESTIAQUE') }} | @lang("Login")</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('assets/img/favicon/Encodex.ico') }}" type="image/x-icon">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="{{ asset('backend/vendor/fontawesome-free/css/all.min.css') }}">
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <style>
        :root {
            --primary-color: #0f2d4a;
            --secondary-color: #0f9bd6;
            --accent-color: #0f9bd6;
            --light-color: #ffffff;
            --text-color: #8d1c1c;
            --light-gray: #f5f5f5;
            --focus-color: limegreen;
        }

        html, body {
            height: 100%;
            margin: 0;
            background: #0f172a; /* dark login-style background */
            color: #fff;
            overflow: hidden;
        }

        /* Particles background */
        #particles-js {
            position: absolute;
            width: 100%;
            height: 100%;
            z-index: 0;
            top: 0;
            left: 0;
        }

        .error-container {
            position: relative;
            z-index: 1;
            text-align: center;
            max-width: 400px;
            margin: auto;
            top: 50%;
            transform: translateY(-50%);
        }

        .error-code {
            font-size: 8rem;
            font-weight: bold;
            color: #1d4ed8; /* Encodex primary */
        }

        .error-message {
            font-size: 1.25rem;
            color: #cbd5e1;
        }

        .btn-encodex {
            background-color: #1d4ed8;
            color: #fff;
            transition: 0.3s;
        }

        .btn-encodex:hover {
            background-color: #2563eb;
            color: #fff;
        }

        .encodex-icon {
            font-size: 6rem;
            color: #1d4ed8;
        }
    </style>


    <style>

        html, body {

            font-family: 'Nunito', sans-serif;
            font-family: cursive;
        }

        .login-card {
            display: flex;
            width: 100%;
            max-width: 1000px;
            /* background-color: var(--light-color); */
            border-radius: 15px;
            overflow: hidden;
            box-shadow:
                0 4px 6px rgba(0, 0, 0, 0.3),  /* subtle close shadow */
                0 10px 20px rgba(0, 0, 0, 0.4), /* deeper shadow */
                0 15px 40px rgba(0, 0, 0, 0.5); /* big, diffused shadow */

        }
        .login-image {
            flex: 1;
            background: url('/assets/img/default-img/login-bg-1.jpeg') center center;
            position: relative;
            min-height: 300px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-size: cover;
        }

        .login-image:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 38, 255, 0.1);
        }

        .login-form {
            flex: 1;
            padding: 40px;
            /* background-color: var(--light-color); */
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .login-header {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 5px;
        }

        .login-avatar {
            position: relative; /* for pseudo elements */
            width: 80px;
            height: 80px;
            /* background-color: white; */
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            border:1px solid var(--accent-color);
            /* Stronger 3D inset shadows for depth */

        }

        /* Glossy highlight */
        .login-avatar::before {
            content: "";
            position: absolute;
            top: 12%;
            left: 12%;
            width: 40%;
            height: 30%;
            border-radius: 50%;
            /* background: rgba(255, 255, 255, 0.2); */
            filter: blur(6px);
            pointer-events: none;
        }

        /* Optional subtle shine */
        .login-avatar::after {
            content: "";
            position: absolute;
            bottom: 18%;
            right: 15%;
            width: 30%;
            height: 30%;
            border-radius: 50%;
            /* background: rgba(255, 255, 255, 0.2); */
            filter: blur(4px);
            pointer-events: none;
            transform: rotate(20deg);
        }



        .login-avatar i {
            font-size: 40px;
            color: white;
        }

        .login-title {
            font-family: cursive;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
            color: var(--accent-color);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: var(--accent-color);
            margin-bottom: 8px;
            text-transform: uppercase;
        }

        .form-control {
            padding: 12px 15px;
            border: 1px solid #002472;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s;
            background: none !important;
            color: whitesmoke !important;
        }

        .form-control:focus {
            border-color: var(--focus-color);
            box-shadow: none;
            outline: none;
        }


        /* .btn-login {
            background-color: #0f2d4a6e;
            color: var(--accent-color);
            border: none;
            padding: 12px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            transition: all 0.3s;
            border: 1px solid #002472;
        } */

        .btn-login {
            position: relative;
            background-color: #0f2d4a6e;
            color: var(--accent-color);
            border: 1px solid #002472;
            padding: 12px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            overflow: hidden;
            transition: all 0.35s ease;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.25);
        }

        /* subtle shine layer */
        .btn-login::before {
            content: "";
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(
                120deg,
                transparent,
                rgba(255, 255, 255, 0.25),
                transparent
            );
            transition: all 0.6s ease;
        }

        /* Hover magic ✨ */
        .btn-login:hover {
            /* background: linear-gradient(135deg, #00e626, #00b81d); */
            background-color: rgba(0, 68, 255, 0.171) !important;
            color: #ffffff;
            transform: translateY(-4px) scale(1.01);
            box-shadow:
                0 6px 15px rgba(0, 230, 38, 0.45),
                0 0 0 2px rgba(0, 230, 38, 0.25);
        }

        /* shine moves */
        .btn-login:hover::before {
            left: 100%;
        }

        /* Click feel */
        .btn-login:active {
            transform: translateY(-1px) scale(0.98);
            box-shadow: 0 6px 14px rgba(0, 230, 38, 0.35);
        }


        .remember-me {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            color: var(--accent-color);
        }

        .remember-me input {
            margin-right: 8px;
        }

        .copyright {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
            color: #777;
        }

        .password-toggle {
            color: #888;
            user-select: none;
        }

        .password-toggle:hover {
            color: var(--primary-color);
        }

        .text-shadow{
            text-shadow: 2px 2px 5px rgba(0,0,0,0.5);
        }
        .box-shadow{
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }


        .alert{
            text-align: center;
            margin-bottom: 20px;
            text-shadow: 0px 3px 4px rgb(255 5 8 / 70%);
            /* animation: pulseGlow 1.5s infinite; */
            animation: shake 0.5s 1;
            /* animation: shake 0.5s infinite, pulseGlow 1.5s infinite; */
            background: none;
            border: none;
        }
        .alert-danger{
            color: #ff0000a1;
        }
        @keyframes pulseGlow {
            0% {
                box-shadow: 0 0 5px rgba(255, 0, 0, 0.5);
            }
            50% {
                box-shadow: 0 0 20px rgba(255, 0, 0, 1);
            }
            100% {
                box-shadow: 0 0 5px rgba(255, 0, 0, 0.5);
            }
        }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20% { transform: translateX(-5px); }
            40% { transform: translateX(5px); }
            60% { transform: translateX(-5px); }
            80% { transform: translateX(5px); }
        }


        /* Responsive adjustments */
        @media (max-width: 768px) {
            .login-card {
                /* min-height: 300px; */
                /* margin-top: -1rem; */
            }

            .login-image {
                display: none; /* Hide image completely on mobile */
            }

            .login-form {
                padding: 30px 20px;
            }

            .form-control {
                /* width: 87%; */
            }
        }


        .custom-checkbox input {
            /* Hide visually but keep focusable */
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
            margin: 0;
            padding: 0;
        }

        .custom-checkbox {
            display: inline-flex;
            align-items: center;
            cursor: pointer;
            user-select: none;
            position: relative;
        }

        /* custom box */
        .custom-checkbox span {
            width: 20px;
            height: 20px;
            display: inline-block;
            background: none;
            border: 1px solid #002472;
            border-radius: 4px;
            margin-right: 8px;
            position: relative;
            transition: background 0.2s, border-color 0.2s, box-shadow 0.2s;
        }

        /* checkmark */
        .custom-checkbox span::after {
            content: "";
            position: absolute;
            display: none;
            left: 7px;
            top: 3px;
            width: 5px;
            height: 10px;
            border: solid white;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }

        /* show checkmark when checked */
        .custom-checkbox input:checked + span::after {
            display: block;
        }

        /* focus effect */
        .custom-checkbox input:focus + span {
            border-color: var(--focus-color);
            outline: none;
        }

        input:-webkit-autofill,
        input:-webkit-autofill:hover,
        input:-webkit-autofill:focus,
        textarea:-webkit-autofill,
        select:-webkit-autofill {
            -webkit-box-shadow: 0 0 0px 1000px #11111100 inset; /* তোমার bg color */
            -webkit-text-fill-color: white; /* text color */
            transition: background-color 5000s ease-in-out 0s;
        }


    </style>

</head>
<body>
    <!-- Particles -->
    <div id="particles-js"></div>
    <div class="error-container animate__animated animate__fadeIn">
        <div class="login-card">
            <div class="login-form">
                <div class="login-header">
                    <div class="login-avatar">
                        {{-- <i class="fas fa-user"></i> --}}
                        {{-- <img loading="lazy" src="{{ asset('assets/img/favicon/Encodex.ico') }}" class="brand-image opacity-75 shadow " style="width: 100%" alt="ENcodeX"> --}}
                        <img loading="lazy" src="{{ get_image('app_logo') ?? asset('assets/img/default-img/Encodex_c.png') }}" class="brand-image opacity-75 shadow " style="width: 100%" alt="ENcodeX">
                    </div>
                    <h1 class="login-title text-shadow">{{ __('WELCOME') }}</h1>
                    <p style="text-align: center; color:#ffffff7a" class="text-shadow">{{ __('Enter your username and password to log in.') }}</p>
                </div>

                @if (session('status'))
                    <div class="alert alert-success mb-4">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger mb-4">
                        @foreach ($errors->all() as $error)
                            {{ $error }}<br>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="form-group ">
                        <label class="text-shadow" for="email">{{ __('EMAIL') }}</label>
                        <input type="email" class="form-control text-shadow box-shadow" id="email" name="email"
                            value="{{ old('email') }}" spellcheck="false" required autofocus>
                    </div>

                    <div class="form-group" style="position: relative;">
                        <label class="text-shadow" for="password">{{ __('PASSWORD') }}</label>
                        <input type="password" class="form-control text-shadow box-shadow" id="password" name="password" required>
                        <span class="password-toggle text-shadow" id="togglePassword" style="position: absolute; right: 15px; top: 41px; cursor: pointer;">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>


                    <div class="remember-me">
                        {{-- <input type="checkbox" id="remember_me" name="remember" class="text-shadow box-shadow"> --}}
                        {{-- <label for="remember_me" class="text-shadow">{{ __('Remember Me') }}</label> --}}
                        <label class="custom-checkbox text-shadow ">
                            <input type="checkbox" class="box-shadow" id="remember_me" name="remember">
                            <span class="box-shadow"></span>
                            {{ __('Remember Me') }}
                        </label>
                    </div>


                    <button type="submit" class="btn-login btn btn-encodex box-shadow">
                        {{ __('LOGIN') }}
                    </button>
                </form>

                <div class="copyright">
                    @lang('mycopyright', [
                        'year' => banglaYear(date('Y')),
                        'company' => get_setting('shop_name', 'Your Company')
                    ])
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Particles.js library -->
    <script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.min.js"></script>
    <script>
        /* Particles.js config */
        particlesJS("particles-js", {
            "particles": {
                "number": { "value": 80, "density": { "enable": true, "value_area": 800 } },
                "color": { "value": "#1d4ed8" },
                "shape": { "type": "circle" },
                "opacity": { "value": 0.5, "random": true },
                "size": { "value": 3, "random": true },
                "line_linked": { "enable": true, "distance": 150, "color": "#1d4ed8", "opacity": 0.4, "width": 1 },
                "move": { "enable": true, "speed": 2, "direction": "none", "random": true, "straight": false, "out_mode": "out" }
            },
            "interactivity": {
                "detect_on": "canvas",
                "events": { "onhover": { "enable": true, "mode": "repulse" }, "onclick": { "enable": true, "mode": "push" } },
                "modes": { "repulse": { "distance": 100 }, "push": { "particles_nb": 4 } }
            },
            "retina_detect": true
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const togglePassword = document.querySelector('#togglePassword');
            const passwordInput = document.querySelector('#password');

            togglePassword.addEventListener('click', function () {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);

                // Toggle the icon class
                this.querySelector('i').classList.toggle('fa-eye');
                this.querySelector('i').classList.toggle('fa-eye-slash');
            });
        });
    </script>
</body>
</html>
