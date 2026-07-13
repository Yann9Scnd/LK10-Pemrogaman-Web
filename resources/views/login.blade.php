<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Secure App</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @keyframes float-1 {
            0% { transform: translate(0px, 0px) scale(1); }
            33% { transform: translate(30px, -50px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
            100% { transform: translate(0px, 0px) scale(1); }
        }
        @keyframes float-2 {
            0% { transform: translate(0px, 0px) scale(1); }
            50% { transform: translate(-40px, 40px) scale(1.15); }
            100% { transform: translate(0px, 0px) scale(1); }
        }
        @keyframes float-3 {
            0% { transform: translate(0px, 0px) scale(1); }
            33% { transform: translate(-25px, -30px) scale(0.9); }
            66% { transform: translate(40px, 20px) scale(1.1); }
            100% { transform: translate(0px, 0px) scale(1); }
        }
        @keyframes card-fade-in {
            0% { opacity: 0; transform: translateY(20px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        @keyframes pulse-soft {
            0%, 100% { transform: scale(1); opacity: 0.6; }
            50% { transform: scale(1.05); opacity: 0.8; }
        }
        .animate-float-1 {
            animation: float-1 12s infinite ease-in-out;
        }
        .animate-float-2 {
            animation: float-2 15s infinite ease-in-out;
        }
        .animate-float-3 {
            animation: float-3 10s infinite ease-in-out;
        }
        .animate-card {
            animation: card-fade-in 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        .animate-pulse-soft {
            animation: pulse-soft 3s infinite ease-in-out;
        }
    </style>
</head>
<body class="bg-gradient-to-tr from-blue-50 via-sky-50 to-indigo-50 text-slate-800 flex items-center justify-center min-h-screen font-sans selection:bg-blue-500 selection:text-white overflow-hidden relative">
    
    <!-- Animated background blobs -->
    <div class="absolute top-10 left-10 w-[450px] h-[450px] bg-blue-200/50 rounded-full blur-3xl pointer-events-none animate-float-1"></div>
    <div class="absolute bottom-10 right-10 w-[500px] h-[500px] bg-sky-200/40 rounded-full blur-3xl pointer-events-none animate-float-2"></div>
    <div class="absolute top-1/3 right-1/4 w-[350px] h-[350px] bg-indigo-200/40 rounded-full blur-3xl pointer-events-none animate-float-3"></div>

    <!-- Login Card -->
    <div class="w-full max-w-md p-8 mx-4 bg-white/70 backdrop-blur-xl border border-white/80 rounded-3xl shadow-2xl shadow-blue-100/50 relative z-10 animate-card opacity-0">
        
        <!-- Icon / Header -->
        <div class="text-center mb-8">
            <div class="relative inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-tr from-blue-600 to-sky-500 text-white mb-4 shadow-lg shadow-blue-500/30">
                <div class="absolute inset-0 rounded-2xl bg-blue-400 blur-md opacity-60 animate-pulse-soft"></div>
                <!-- Shield Icon SVG -->
                <svg class="w-8 h-8 relative z-10" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"></path>
                </svg>
            </div>
            <h1 class="text-3xl font-extrabold tracking-tight bg-gradient-to-r from-blue-700 via-blue-600 to-sky-500 bg-clip-text text-transparent">
                Secure App
            </h1>
            <p class="text-sm text-slate-500 mt-2 font-medium">Gateway Keamanan Terpadu</p>
        </div>

        @if(session('error') || request()->query('error'))
            <div class="mb-6 p-4 bg-rose-50 border border-rose-100 text-rose-600 text-sm rounded-2xl leading-relaxed shadow-sm">
                <div class="flex gap-2">
                    <svg class="w-5 h-5 text-rose-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"></path>
                    </svg>
                    <div>
                        <strong class="block font-semibold">Gagal Terhubung:</strong>
                        <span class="text-rose-500/90">{{ session('error') ?? request()->query('error') }}</span>
                    </div>
                </div>
            </div>
        @endif

        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-100 text-emerald-600 text-sm rounded-2xl shadow-sm flex gap-2">
                <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        <!-- Login Button Section -->
        <div class="space-y-5">
            <a href="{{ route('login') }}" class="group relative flex items-center justify-center gap-3 w-full py-4 px-4 bg-gradient-to-r from-blue-600 to-sky-500 hover:from-blue-700 hover:to-sky-600 text-white font-bold rounded-2xl shadow-lg shadow-blue-500/20 hover:shadow-blue-500/35 transition-all duration-300 transform hover:-translate-y-0.5 text-center">
                <span>Hubungkan dengan WorkOS AuthKit</span>
                <svg class="w-5 h-5 transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"></path>
                </svg>
            </a>
            <div class="flex items-center gap-2 justify-center">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-ping"></span>
                <p class="text-center text-xs text-slate-400 font-medium">
                    Koneksi terenkripsi ke portal Single Sign-On resmi WorkOS.
                </p>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="mt-8 pt-6 border-t border-slate-100 text-center">
            <span class="text-xs text-slate-400 font-semibold tracking-wide">
                Dikembangkan oleh <span class="text-blue-600 font-bold">Muhammad Mardiansyah</span>
            </span>
        </div>
    </div>
</body>
</html>
