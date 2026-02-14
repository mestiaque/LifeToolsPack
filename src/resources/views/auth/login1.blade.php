<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'EncodeX') }} | @lang("Login")</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito:400,500,600,700&display=swap">

    <!-- FontAwesome -->
    <link rel="stylesheet" href="{{ asset('backend/vendor/fontawesome-free/css/all.min.css') }}">

    <!-- Bootstrap CSS -->

            <link rel="icon" href="{{ asset('assets/img/favicon/Encodex.ico') }}" type="image/x-icon">
    <link href="{{ asset('css/encodex.css') }}" rel="stylesheet">


    <!-- Custom styles for login page -->
    <style>
        :root {
            --primary-color: #0f2d4a;
            --secondary-color: #0f9bd6;
            --accent-color: #222222;
            --light-color: #ffffff;
            --text-color: #333333;
            --light-gray: #f5f5f5;
        }

        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            font-family: 'Nunito', sans-serif;
            background-color: var(--light-gray);
            font-family: cursive;
        }
        body{
            overflow: hidden;
        }

        .login-container {
            display: flex;
            height: 100vh;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-card {
            display: flex;
            width: 100%;
            max-width: 1000px;
            background-color: var(--light-color);
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
            background-color: var(--light-color);
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
            background-color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;

            /* Stronger 3D inset shadows for depth */
            box-shadow:
                inset 6px 6px 8px rgba(0, 0, 0, 0.15),    /* deep shadow bottom-right */
                inset -6px -6px 8px rgba(255, 255, 255, 0.9), /* bright highlight top-left */
                5px 5px 15px rgba(0, 0, 0, 0.2);           /* pronounced outer shadow */
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
            background: rgba(255, 255, 255, 0.85);
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
            background: rgba(255, 255, 255, 0.5);
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
            width: 92%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(255, 204, 0, 0.2);
            outline: none;
        }

        .btn-login {
            background-color: var(--primary-color);
            color: var(--accent-color);
            border: none;
            padding: 12px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            transition: all 0.3s;
        }

        .btn-login:hover {
            background-color: #e6b800;
            transform: translateY(-2px);
        }

        .remember-me {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
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

        .binary-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            overflow: hidden;
            background-color: #0A0D25;
            color: #FF000047;
            font-family: monospace;
            font-size: 16px;
            line-height: 20px;
            white-space: pre;
            z-index: 0; /* stays in back */
        }

        .login-container {
            position: relative;
            z-index: 1; /* stays in front */
        }

        .alert{
            text-align: center;
            margin-bottom: 20px;
            text-shadow: 0px 3px 4px rgb(255 5 8 / 70%);
            /* animation: pulseGlow 1.5s infinite; */
            animation: shake 0.5s 1;
            /* animation: shake 0.5s infinite, pulseGlow 1.5s infinite; */
        }
        .alert-danger{
            color: red;
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
                flex-direction: column;
                background: url('/assets/img/default-img/login-bg-1.jpeg') center center;
                background-size: cover;
                background-repeat: no-repeat;
                background-color: transparent; /* ensure no white bg */
                min-height: 300px;
                border: 1px solid #ccc; /* for debugging */
                margin-top: -1rem;
            }

            .login-image {
                display: none; /* Hide image completely on mobile */
            }

            .login-form {
                padding: 30px 20px;
                background-color: #ffffffe3 !important;
            }

            .form-control {
                width: 87%;
            }
        }

    </style>
</head>
<body>
    {{-- <div class="binary-bg" id="binary"></div> --}}
    <canvas id="binary" class="binary-bg" style=""></canvas>


    <div class="login-container">
        <div class="login-card">
            <div class="login-image">
                <!-- This div uses the background image -->
            </div>
            <div class="login-form">
                <div class="login-header">
                    <div class="login-avatar">
                        {{-- <i class="fas fa-user"></i> --}}
                        <img loading="lazy" src="{{ asset('assets/img/favicon/Encodex.ico') }}" class="brand-image opacity-75 shadow " style="width: 100%" alt="ENcodeX">
                    </div>
                    <h1 class="login-title text-shadow">{{ __('WELCOME') }}</h1>
                    <p style="text-align: center;" class="text-shadow">{{ __('Enter your username and password to log in.') }}</p>
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
                               value="{{ old('email') }}" required autofocus>
                    </div>

                    <div class="form-group" style="position: relative;">
                        <label class="text-shadow" for="password">{{ __('PASSWORD') }}</label>
                        <input type="password" class="form-control text-shadow box-shadow" id="password" name="password" required>
                        <span class="password-toggle text-shadow" id="togglePassword" style="position: absolute; right: 15px; top: 38px; cursor: pointer;">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>


                    <div class="remember-me">
                        <input type="checkbox" id="remember_me" name="remember" class="text-shadow box-shadow">
                        <label for="remember_me" class="text-shadow">{{ __('Remember Me') }}</label>
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
    <script src="{{ asset('backend/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('backend/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
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
    {{-- <script>
        const canvas = document.getElementById('binary');
        const ctx = canvas.getContext('2d');

        // full screen size
        canvas.height = window.innerHeight;
        canvas.width = window.innerWidth;

        const binaryChars = "01";
        const fontSize = 16;
        const columns = Math.floor(canvas.width / fontSize);

        // প্রতিটি column এর drop position
        const drops = Array(columns).fill(1);

        function draw() {
            // ফেইড effect (ট্রেইল)
            ctx.fillStyle = "rgba(0, 0, 0, 0.05)";
            ctx.fillRect(0, 0, canvas.width, canvas.height);

            ctx.fillStyle = "#0F0"; // সবুজ রঙ
            ctx.font = fontSize + "px monospace";

            drops.forEach((y, i) => {
                const text = binaryChars.charAt(Math.floor(Math.random() * binaryChars.length));
                ctx.fillText(text, i * fontSize, y * fontSize);

                // নিচে নামা
                if (y * fontSize > canvas.height && Math.random() > 0.975) {
                    drops[i] = 0;
                }
                drops[i]++;
            });
        }

        setInterval(draw, 33); // প্রায় 30fps
    </script> --}}

    <script>
        const canvas = document.getElementById('binary');
        const ctx = canvas.getContext('2d');

        // full screen
        canvas.height = window.innerHeight;
        canvas.width = window.innerWidth;

        // এখানে company name ব্যবহার করা হচ্ছে
        const letters = "ESTIAQUE";
        const fontSize = 16;
        const columns = Math.floor(canvas.width / fontSize);

        // প্রতিটি column এর drop অবস্থান
        const drops = Array(columns).fill(1);

        function draw() {
            // ট্রেইল effect
            ctx.fillStyle = "rgba(0, 0, 0, 0.05)";
            ctx.fillRect(0, 0, canvas.width, canvas.height);

            ctx.fillStyle = "#0F0"; // সবুজ
            ctx.font = fontSize + "px monospace";

            drops.forEach((y, i) => {
                const text = letters.charAt(Math.floor(Math.random() * letters.length));
                ctx.fillText(text, i * fontSize, y * fontSize);

                // নিচে নামা
                if (y * fontSize > canvas.height && Math.random() > 0.975) {
                    drops[i] = 0;
                }
                drops[i]++;
            });
        }

        setInterval(draw, 33); // ~30fps
    </script>


</body>
</html>
