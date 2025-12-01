<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Register - Inventaris Barang</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- TailwindCSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Font & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 50%, #bae6fd 100%);
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes scaleIn {
            from {
                transform: scale(0.95);
                opacity: 0;
            }

            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        @keyframes gradientBG {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        .animate-float {
            animation: float 6s ease-in-out infinite;
        }

        .animate-fadeIn {
            animation: fadeIn 0.8s cubic-bezier(0.22, 1, 0.36, 1);
        }

        .animate-scaleIn {
            animation: scaleIn 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .gradient-bg {
            background: linear-gradient(-45deg, #f0f9ff, #e0f2fe, #bae6fd, #7dd3fc);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
        }

        .input-field {
            transition: all 0.3s ease;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .input-field:focus {
            box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.2);
        }

        .btn-primary {
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px -1px rgba(14, 165, 233, 0.3), 0 2px 4px -1px rgba(14, 165, 233, 0.1);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(14, 165, 233, 0.3), 0 4px 6px -2px rgba(14, 165, 233, 0.1);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .floating-shapes {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: -1;
        }

        .shape {
            position: absolute;
            opacity: 0.2;
            border-radius: 50%;
            background: linear-gradient(45deg, #0ea5e9, #7dd3fc);
            filter: blur(40px);
        }

        .password-strength {
            height: 4px;
            margin-top: 4px;
            border-radius: 2px;
            transition: all 0.3s ease;
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center px-4 py-12 gradient-bg">
    <!-- Floating background shapes -->
    <div class="floating-shapes">
        <div class="shape w-64 h-64 top-10 left-10 animate-float" style="animation-delay: 0s;"></div>
        <div class="shape w-80 h-80 bottom-20 right-20 animate-float" style="animation-delay: 2s;"></div>
        <div class="shape w-96 h-96 top-1/3 right-1/4 animate-float" style="animation-delay: 4s;"></div>
    </div>

    <div class="w-full max-w-md bg-white/90 backdrop-blur-sm rounded-2xl shadow-2xl p-8 animate-scaleIn">
        <div class="text-center mb-8 animate-fadeIn" style="animation-delay: 0.2s;">
            <div class="w-20 h-20 bg-primary-100 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-inner">
                <i class="fas fa-user-plus text-primary-600 text-3xl"></i>
            </div>
            <h2 class="text-3xl font-bold text-slate-800 mb-2">Buat Akun Baru</h2>
            <p class="text-slate-500">Daftar untuk mengakses sistem inventaris</p>
        </div>

        @if ($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded mb-6 text-sm animate-fadeIn" style="animation-delay: 0.3s;">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <div>
                    @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <form method="POST" action="{{ route('register') }}" class="space-y-5">
            @csrf

            <div class="animate-fadeIn" style="animation-delay: 0.4s;">
                <label for="name" class="block text-sm font-medium text-slate-700 mb-2">Nama Lengkap</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-user text-slate-400"></i>
                    </div>
                    <input type="text" id="name" name="name"
                        class="w-full pl-10 pr-4 py-3 border border-slate-200 rounded-xl input-field focus:border-primary-500 focus:ring-primary-500"
                        placeholder="Nama Anda"
                        value="{{ old('name') }}" required autofocus>
                </div>
            </div>

            <div class="animate-fadeIn" style="animation-delay: 0.5s;">
                <label for="email" class="block text-sm font-medium text-slate-700 mb-2">Alamat Email</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-envelope text-slate-400"></i>
                    </div>
                    <input type="email" id="email" name="email"
                        class="w-full pl-10 pr-4 py-3 border border-slate-200 rounded-xl input-field focus:border-primary-500 focus:ring-primary-500"
                        placeholder="email@contoh.com"
                        value="{{ old('email') }}" required>
                </div>
            </div>

            <div class="animate-fadeIn" style="animation-delay: 0.6s;">
                <label for="password" class="block text-sm font-medium text-slate-700 mb-2">Password</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-lock text-slate-400"></i>
                    </div>
                    <input type="password" id="password" name="password"
                        class="w-full pl-10 pr-4 py-3 border border-slate-200 rounded-xl input-field focus:border-primary-500 focus:ring-primary-500"
                        placeholder="••••••••"
                        required
                        oninput="checkPasswordStrength(this.value)">
                    <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center" id="togglePassword">
                        <i class="fas fa-eye text-slate-400 hover:text-primary-500 cursor-pointer"></i>
                    </button>
                </div>
                <div class="flex gap-1 mt-1">
                    <div id="strength-1" class="password-strength w-full bg-slate-200"></div>
                    <div id="strength-2" class="password-strength w-full bg-slate-200"></div>
                    <div id="strength-3" class="password-strength w-full bg-slate-200"></div>
                    <div id="strength-4" class="password-strength w-full bg-slate-200"></div>
                </div>
                <p id="password-hint" class="text-xs text-slate-500 mt-1">Gunakan minimal 8 karakter dengan kombinasi huruf dan angka</p>
            </div>

            <div class="animate-fadeIn" style="animation-delay: 0.7s;">
                <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-2">Konfirmasi Password</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-lock text-slate-400"></i>
                    </div>
                    <input type="password" id="password_confirmation" name="password_confirmation"
                        class="w-full pl-10 pr-4 py-3 border border-slate-200 rounded-xl input-field focus:border-primary-500 focus:ring-primary-500"
                        placeholder="••••••••"
                        required>
                    <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center" id="toggleConfirmPassword">
                        <i class="fas fa-eye text-slate-400 hover:text-primary-500 cursor-pointer"></i>
                    </button>
                </div>
            </div>

            <div class="animate-fadeIn" style="animation-delay: 0.8s;">
                <button type="submit"
                    class="w-full bg-primary-600 text-white py-3 px-4 rounded-xl btn-primary font-semibold">
                    Daftar Sekarang
                    <i class="fas fa-arrow-right ml-2"></i>
                </button>
            </div>
        </form>

        <div class="text-center text-sm mt-6 animate-fadeIn" style="animation-delay: 1s;">
            <span class="text-slate-600">Sudah punya akun?</span>
            <a href="{{ route('login') }}" class="text-primary-600 font-medium hover:underline ml-1">Masuk di sini</a>
        </div>
    </div>

    <script>
        // Toggle password visibility
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');
        const toggleConfirmPassword = document.querySelector('#toggleConfirmPassword');
        const confirmPassword = document.querySelector('#password_confirmation');

        togglePassword.addEventListener('click', function(e) {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.querySelector('i').classList.toggle('fa-eye');
            this.querySelector('i').classList.toggle('fa-eye-slash');
        });

        toggleConfirmPassword.addEventListener('click', function(e) {
            const type = confirmPassword.getAttribute('type') === 'password' ? 'text' : 'password';
            confirmPassword.setAttribute('type', type);
            this.querySelector('i').classList.toggle('fa-eye');
            this.querySelector('i').classList.toggle('fa-eye-slash');
        });

        // Password strength indicator
        function checkPasswordStrength(password) {
            const strengthBars = [
                document.getElementById('strength-1'),
                document.getElementById('strength-2'),
                document.getElementById('strength-3'),
                document.getElementById('strength-4')
            ];
            const hint = document.getElementById('password-hint');

            // Reset all bars
            strengthBars.forEach(bar => {
                bar.classList.remove('bg-red-500', 'bg-yellow-500', 'bg-green-500');
                bar.classList.add('bg-slate-200');
            });

            if (password.length === 0) {
                hint.textContent = "Gunakan minimal 8 karakter dengan kombinasi huruf dan angka";
                hint.classList.remove('text-red-500', 'text-green-500');
                hint.classList.add('text-slate-500');
                return;
            }

            if (password.length < 6) {
                strengthBars[0].classList.remove('bg-slate-200');
                strengthBars[0].classList.add('bg-red-500');
                hint.textContent = "Password terlalu pendek";
                hint.classList.remove('text-slate-500', 'text-green-500');
                hint.classList.add('text-red-500');
                return;
            }

            // Check password strength
            let strength = 0;

            // Length
            if (password.length >= 8) strength++;
            if (password.length >= 12) strength++;

            // Contains numbers
            if (/\d/.test(password)) strength++;

            // Contains special chars
            if (/[!@#$%^&*(),.?":{}|<>]/.test(password)) strength++;

            // Update bars
            for (let i = 0; i < strength; i++) {
                strengthBars[i].classList.remove('bg-slate-200');

                if (strength <= 2) {
                    strengthBars[i].classList.add('bg-red-500');
                    hint.textContent = "Password lemah";
                    hint.classList.remove('text-slate-500', 'text-green-500');
                    hint.classList.add('text-red-500');
                } else if (strength === 3) {
                    strengthBars[i].classList.add('bg-yellow-500');
                    hint.textContent = "Password cukup kuat";
                    hint.classList.remove('text-slate-500', 'text-red-500');
                    hint.classList.add('text-green-500');
                } else {
                    strengthBars[i].classList.add('bg-green-500');
                    hint.textContent = "Password sangat kuat!";
                    hint.classList.remove('text-slate-500', 'text-red-500');
                    hint.classList.add('text-green-500');
                }
            }
        }

        // Add ripple effect to buttons
        document.querySelectorAll('.btn-primary').forEach(button => {
            button.addEventListener('click', function(e) {
                const x = e.clientX - e.target.getBoundingClientRect().left;
                const y = e.clientY - e.target.getBoundingClientRect().top;

                const ripple = document.createElement('span');
                ripple.className = 'ripple-effect';
                ripple.style.left = `${x}px`;
                ripple.style.top = `${y}px`;

                px;

                this.appendChild(ripple);

                setTimeout(() => {
                    ripple.remove();
                }, 1000);
            });
        });
    </script>
</body>

</html>