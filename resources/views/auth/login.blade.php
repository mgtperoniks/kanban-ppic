<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - KANBAN PPIC</title>
    <script src="{{ asset('js/tailwindcss.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('css/all.min.css') }}">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .login-bg {
            background-image: linear-gradient(rgba(15, 23, 42, 0.4), rgba(15, 23, 42, 0.4)), url("{{ asset('assets/img/login-bg.jpg') }}");
            background-size: cover;
            background-position: center;
        }

        .glass-badge {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(4px);
            border: 1px border rgba(255, 255, 255, 0.2);
        }
    </style>
</head>

<body class="bg-slate-50 min-h-screen flex overflow-hidden">

    <!-- Left Side: Login Form -->
    <div
        class="w-full lg:w-[400px] xl:w-[450px] flex flex-col p-6 lg:p-8 xl:p-12 justify-between bg-white z-10 shadow-2xl overflow-y-auto">
        <div>
            <!-- Logo Section -->
            <div class="mb-8">
                <img src="{{ asset('assets/img/logo-peroni.png') }}" alt="Peroni Logo" class="h-20 w-auto">
            </div>

            <!-- Header Section -->
            <div class="mb-6">
                <h1 class="text-2xl xl:text-3xl font-extrabold text-slate-800 tracking-tight leading-none mb-2">
                    KANBAN - PPIC system
                </h1>
                <div class="h-1 w-16 bg-blue-600 rounded-full mb-3"></div>
                <p class="text-slate-500 font-medium text-sm">Master Data System</p>
            </div>

            <!-- Form Section -->
            <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Email Input -->
                <div class="space-y-1">
                    <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Email
                        Address</label>
                    <div class="relative group">
                        <div
                            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none transition-colors group-focus-within:text-blue-600">
                            <i class="fas fa-envelope text-slate-400 text-sm"></i>
                        </div>
                        <input type="email" name="email" id="email"
                            class="block w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 text-slate-900 rounded-lg focus:ring-2 focus:ring-blue-100 focus:border-blue-600 focus:bg-white placeholder-slate-400 transition-all duration-200 outline-none text-sm"
                            placeholder="name@peroniks.com" required value="{{ old('email') }}">
                    </div>
                    @error('email')
                        <p class="mt-1 text-xs text-red-500 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Input -->
                <div class="space-y-1">
                    <label for="password"
                        class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Password</label>
                    <div class="relative group">
                        <div
                            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none transition-colors group-focus-within:text-blue-600">
                            <i class="fas fa-lock text-slate-400 text-sm"></i>
                        </div>
                        <input type="password" name="password" id="password"
                            class="block w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 text-slate-900 rounded-lg focus:ring-2 focus:ring-blue-100 focus:border-blue-600 focus:bg-white placeholder-slate-400 transition-all duration-200 outline-none text-sm"
                            placeholder="••••••••" required>
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center">
                    <input id="remember" name="remember" type="checkbox"
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-slate-300 rounded cursor-pointer">
                    <label for="remember" class="ml-2 block text-xs text-slate-600 font-medium cursor-pointer">
                        Remember me
                    </label>
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button type="submit"
                        class="w-full flex items-center justify-center gap-2 py-3 px-6 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-bold rounded-lg shadow-lg shadow-blue-200 hover:shadow-blue-300 transition-all duration-200 transform hover:-translate-y-0.5">
                        <span>Sign In</span>
                        <i class="fas fa-arrow-right text-xs"></i>
                    </button>
                </div>
            </form>
        </div>

        <!-- Footer Section -->
        <div class="mt-8">
            <p class="text-[11px] text-slate-400 leading-relaxed">
                &copy; {{ date('Y') }} PPIC DEPT, <span class="font-semibold">PT Peroni Karya Sentra.</span><br>
                All rights reserved.
            </p>
        </div>
    </div>

    <!-- Right Side: Background Image & Text -->
    <div class="hidden lg:flex flex-1 login-bg relative flex-col justify-end p-12 xl:p-16 overflow-hidden">
        <!-- Visual Overlay -->
        <div class="absolute inset-0 bg-gradient-to-t from-slate-900/90 via-slate-900/40 to-transparent"></div>

        <!-- Content Overlay -->
        <div class="relative z-10 max-w-xl">
            <div class="flex items-center gap-3 mb-4">
                <div
                    class="flex items-center gap-2 px-3 py-1 bg-emerald-500/20 backdrop-blur-md rounded-full border border-emerald-500/30">
                    <div class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></div>
                    <span class="text-[10px] font-bold text-emerald-400 tracking-wider">SYSTEM ONLINE</span>
                </div>
            </div>

            <h2 class="text-4xl xl:text-5xl font-bold text-white leading-tight mb-4 tracking-tight">
                Secure Master Data<br>
                & Centralized Records
            </h2>
            <p class="text-base xl:text-lg text-slate-200 leading-relaxed mb-10 opacity-90 font-light">
                Sistem terpusat untuk pengelolaan data master, memastikan integritas dan akurasi informasi perusahaan.
            </p>

            <div class="flex gap-10">
                <div class="space-y-1">
                    <p class="text-2xl font-bold text-white">Central</p>
                    <p class="text-[9px] font-bold text-slate-400 tracking-widest uppercase">DATABASE</p>
                    <div class="h-0.5 w-full bg-blue-500/50"></div>
                </div>
                <div class="space-y-1">
                    <p class="text-2xl font-bold text-white">Secure</p>
                    <p class="text-[9px] font-bold text-slate-400 tracking-widest uppercase">ACCESS</p>
                    <div class="h-0.5 w-full bg-blue-500/50"></div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>