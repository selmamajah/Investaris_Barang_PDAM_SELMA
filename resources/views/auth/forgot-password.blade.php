<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Lupa Password - Inventaris Barang PDAM Surabaya</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #1a1a1a; 
            color: #e0e0e0; 
        }

        /* Grayscale Palette Variables */
        :root {
            --dark-bg-main: #1a1a1a;
            --dark-bg-card: #2d2d2d; 
            --text-light: #f0f0f0; 
            --text-medium: #b0b0b0;
            --text-dark: #7a7a7a;
            --border-gray: #4a4a4a;
            --accent-gray-light: #9CA3AF; 
            --accent-gray-hover: #6B7280; 
            --accent-gray-ink: #555555; 
            --focus-ring-gray: rgba(156, 163, 175, 0.4); 
            --error-bg-dark: rgba(220, 38, 38, 0.2); 
            --error-text-dark: #f87171; 
            --success-bg-dark: rgba(22, 163, 74, 0.2); 
            --success-text-dark: #4ade80; 
        }

        .bg-main { background-color: var(--dark-bg-main); }
        .bg-card { background-color: var(--dark-bg-card); }
        .text-light { color: var(--text-light); }
        .text-medium { color: var(--text-medium); }
        .text-dark { color: var(--text-dark); }
        .border-gray { border-color: var(--border-gray); }
        .bg-accent-light { background-color: var(--accent-gray-light); }
        .hover\:bg-accent-hover:hover { background-color: var(--accent-gray-hover); }
        .text-accent-light { color: var(--accent-gray-light); }
        .hover\:text-light:hover { color: var(--text-light); }
        .focus\:ring-gray:focus {
            --tw-ring-color: var(--focus-ring-gray);
            border-color: var(--accent-gray-light);
        }

        /* PERBAIKAN: Input Field Styling Sederhana */
        .input-field {
            background-color: var(--dark-bg-card); /* Match card background */
            color: var(--text-light); 
            border: 1px solid var(--border-gray);
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
            /* Padding standard dengan ruang untuk ikon kiri */
            padding: 0.75rem 0.75rem 0.75rem 2.5rem; /* py-3 pl-10 pr-3 */
            appearance: none; 
        }
        .input-field::placeholder { 
            color: var(--text-dark); /* Placeholder terlihat */
        }
        .input-field:focus {
            border-color: var(--accent-gray-light);
            box-shadow: 0 0 0 2px var(--focus-ring-gray);
            outline: none;
        }
        /* PERBAIKAN: Posisi Ikon Input */
 .input-icon {
    position: absolute;
    /* Ganti inset-y-0 dengan top-0 dan height full */
    top: 0; 
    height: 100%; 
    left: 0;
    padding-left: 0.875rem; /* pl-3.5 */
    display: flex;
    align-items: center; /* Tetap center vertikal */
    pointer-events: none;
    color: var(--text-dark);
    transition: color 0.2s ease;
 }
         /* PERBAIKAN: Input container tidak perlu lagi */
         
        /* Ink Bleed Button Effect */
        .btn-primary {
            position: relative;
            overflow: hidden;
            transition: background-color 0.3s ease, color 0.3s ease, box-shadow 0.3s ease;
            z-index: 1; 
            background-color: var(--accent-gray-light);
            color: var(--dark-bg-card); /* Teks Awal Gelap */
        }
        .btn-primary::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background-color: var(--accent-gray-ink); 
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.4s ease-out, height 0.4s ease-out; 
            z-index: -1;
        }
        .btn-primary:hover::before {
            width: 150%; 
            height: 300px; 
        }
         .btn-primary:hover {
            background-color: var(--accent-gray-light); 
            color: var(--text-light); /* Teks Hover Terang */
             box-shadow: 0 4px 15px rgba(0,0,0,0.2); 
        }
         .btn-primary:active {
             transform: scale(0.98); 
         }

        /* Smooth Animations */
        @keyframes fadeInSlideUp { 
            from { opacity: 0; transform: translateY(8px); } 
            to { opacity: 1; transform: translateY(0); } 
        }
        @keyframes scaleIn { 
            from { transform: scale(0.97); opacity: 0; } 
            to { transform: scale(1); opacity: 1; } 
        }

        .animate-fadeInSlideUp { animation: fadeInSlideUp 0.5s ease-out forwards; }
        .animate-scaleIn { animation: scaleIn 0.4s ease-out forwards; }
        
        /* Dark mode notification styles */
        .success-message {
            background-color: var(--success-bg-dark);
            border-color: #16a34a; 
            color: var(--success-text-dark);
        }
         .error-message {
            background-color: var(--error-bg-dark);
            border-color: #dc2626; 
            color: var(--error-text-dark);
        }

        [x-cloak] { display: none !important; }
    </style>
</head>

<body class="flex items-center justify-center min-h-screen px-4 py-12 bg-main">

    <div class="w-full max-w-md p-8 sm:p-10 bg-card rounded-xl shadow-xl animate-scaleIn">
        
        <div class="text-center mb-8 animate-fadeInSlideUp" style="animation-delay: 0.1s;">
             <div class="w-16 h-16 bg-gray-700 rounded-xl flex items-center justify-center mx-auto mb-4">
                 <i class="fas fa-key text-gray-300 text-3xl"></i> 
            </div>
            <h1 class="text-2xl font-bold text-light mb-1">Lupa Password</h1>
            <p class="text-sm text-medium">Masukkan email Anda untuk menerima link reset password.</p>
        </div>

        @if (session('status'))
            <div class="border-l-4 p-4 rounded mb-6 text-sm animate-fadeInSlideUp success-message" style="animation-delay: 0.2s;">
                 {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="border-l-4 p-4 rounded mb-6 text-sm animate-fadeInSlideUp error-message" style="animation-delay: 0.2s;">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('password.email') }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="relative animate-fadeInSlideUp" style="animation-delay: 0.3s;">
                <label for="email" class="block text-sm font-medium text-medium mb-1.5">Email</label>
                <div class="relative"> 
                    <div class="input-icon"> <i class="fas fa-envelope"></i>
                    </div>
                    <input type="email" name="email" id="email" required
                        class="w-full rounded-lg input-field focus:ring-gray" 
                        placeholder="alamat_email@contoh.com" 
                        value="{{ old('email') }}"> 
                 </div>
            </div>

            <button type="submit"
                class="relative w-full text-light py-3 px-4 rounded-lg font-semibold transition-all duration-200 shadow hover:shadow-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent-light focus:ring-offset-gray-800 animate-fadeInSlideUp btn-primary"
                style="animation-delay: 0.4s;">
                <span class="relative z-10">
                    Kirim Link Reset
                </span>
            </button>
        </form>

        <div class="text-center text-sm text-medium mt-8 animate-fadeInSlideUp" style="animation-delay: 0.6s;">
            <a href="{{ route('login') }}" class="text-accent-light hover:text-light hover:underline transition-colors duration-150">
                <i class="fas fa-arrow-left mr-1.5 text-xs"></i> Kembali ke login
            </a>
        </div>
    </div>

    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    </body>
</html>