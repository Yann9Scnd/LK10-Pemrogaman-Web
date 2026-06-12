<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Aplikasi Secure Ceo Adyatma86</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-950 text-slate-100 flex items-center justify-center min-h-screen font-sans selection:bg-indigo-500 selection:text-white overflow-hidden relative">
    
    <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-indigo-600/20 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-purple-600/20 rounded-full blur-3xl pointer-events-none"></div>

    <div class="w-full max-w-md p-8 mx-4 bg-slate-900/60 backdrop-blur-xl border border-slate-800 rounded-2xl shadow-2xl relative z-10">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-extrabold tracking-tight bg-gradient-to-r from-indigo-400 to-purple-400 bg-clip-text text-transparent">
                Gateway Keamanan
            </h1>
            <p class="text-sm text-slate-400 mt-2">Masuk secara aman menggunakan integrasi WorkOS</p>
        </div>

        @if(session('error') || request()->query('error'))
            <div class="mb-6 p-4 bg-rose-500/10 border border-rose-500/30 text-rose-400 text-sm rounded-xl leading-relaxed">
                <strong class="block mb-1">Gagal Terhubung:</strong>
                {{ session('error') ?? request()->query('error') }}
            </div>
        @endif

        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-sm rounded-xl">
                {{ session('success') }}
            </div>
        @endif

        <div class="space-y-4">
            <a href="{{ route('login') }}" class="flex items-center justify-center gap-3 w-full py-3 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl shadow-lg shadow-indigo-600/30 transition-all duration-300 transform hover:-translate-y-0.5 text-center">
                <span>Hubungkan dengan WorkOS AuthKit</span>
            </a>
            <p class="text-center text-xs text-slate-500">
                Menghubungkan langsung ke portal Single Sign-On resmi WorkOS.
            </p>
        </div>
        
        <div class="mt-8 pt-6 border-t border-slate-800/80 text-center">
            <span class="text-xs text-slate-500">Dikembangkan untuk Ceo Adyatma86</span>
        </div>
    </div>
</body>
</html>
