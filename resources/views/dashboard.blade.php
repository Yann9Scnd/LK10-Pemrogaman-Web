<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Secure App</title>
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
        @keyframes fade-in-up {
            0% { opacity: 0; transform: translateY(15px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        .animate-float-1 {
            animation: float-1 15s infinite ease-in-out;
        }
        .animate-float-2 {
            animation: float-2 18s infinite ease-in-out;
        }
        .animate-float-3 {
            animation: float-3 12s infinite ease-in-out;
        }
        .animate-fade-in {
            animation: fade-in-up 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
    </style>
</head>
<body class="bg-gradient-to-tr from-blue-50 via-sky-50 to-indigo-50 text-slate-800 min-h-screen font-sans selection:bg-blue-500 selection:text-white relative overflow-x-hidden">

    <!-- Animated background blobs -->
    <div class="fixed top-10 left-10 w-[500px] h-[500px] bg-blue-200/40 rounded-full blur-3xl pointer-events-none animate-float-1"></div>
    <div class="fixed bottom-10 right-10 w-[600px] h-[600px] bg-sky-200/30 rounded-full blur-3xl pointer-events-none animate-float-2"></div>
    <div class="fixed top-1/2 left-1/3 w-[400px] h-[400px] bg-indigo-200/30 rounded-full blur-3xl pointer-events-none animate-float-3"></div>

    <!-- Navigation Header -->
    <nav class="relative z-10 border-b border-white/60 bg-white/40 backdrop-blur-md shadow-sm">
        <div class="max-w-4xl mx-auto px-4 py-4 flex items-center justify-between">
            <h1 class="text-xl font-extrabold tracking-tight bg-gradient-to-r from-blue-700 via-blue-600 to-sky-500 bg-clip-text text-transparent">
                Secure App
            </h1>
            <div class="flex items-center gap-4">
                <span class="text-sm font-semibold text-slate-600 bg-blue-100/50 px-3 py-1.5 rounded-xl border border-blue-200/40">
                    {{ Auth::user()->name }}
                </span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm font-bold text-rose-500 hover:text-rose-600 transition-colors bg-rose-50 hover:bg-rose-100 px-3 py-1.5 rounded-xl border border-rose-100">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="relative z-10 max-w-4xl mx-auto px-4 py-10 space-y-8 animate-fade-in opacity-0">

        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-100 text-emerald-600 text-sm font-medium rounded-2xl shadow-sm flex gap-2">
                <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- Profile Info Card -->
            <div class="bg-white/70 backdrop-blur-xl border border-white/80 rounded-3xl p-6 shadow-xl shadow-blue-100/30">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-2.5 rounded-xl bg-blue-50 text-blue-600 border border-blue-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"></path>
                        </svg>
                    </div>
                    <h2 class="text-lg font-bold text-slate-800">Profil Pengguna</h2>
                </div>
                <div class="space-y-4 text-sm font-medium">
                    <div class="flex justify-between py-2 border-b border-slate-100">
                        <span class="text-slate-400">ID Pengguna</span>
                        <span class="text-slate-700 font-bold font-mono">{{ Auth::id() }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-slate-100">
                        <span class="text-slate-400">Nama Lengkap</span>
                        <span class="text-slate-700 font-bold">{{ Auth::user()->name }}</span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="text-slate-400">Alamat Email</span>
                        <span class="text-slate-700 font-bold">{{ Auth::user()->email }}</span>
                    </div>
                </div>
            </div>

            <!-- API Endpoint Card -->
            <div class="bg-white/70 backdrop-blur-xl border border-white/80 rounded-3xl p-6 shadow-xl shadow-blue-100/30 flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <div class="p-2.5 rounded-xl bg-sky-50 text-sky-600 border border-sky-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 6.75L22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3l-4.5 16.5"></path>
                            </svg>
                        </div>
                        <h2 class="text-lg font-bold text-slate-800">API Endpoint</h2>
                    </div>
                    <p class="text-sm text-slate-500 mb-6 font-medium leading-relaxed">
                        Akses informasi sesi profil pengguna yang terautentikasi dalam format data JSON melalui endpoint API yang aman.
                    </p>
                </div>
                <a href="{{ route('api.user') }}" target="_blank"
                   class="group flex items-center justify-center gap-2 w-full py-3 px-4 bg-gradient-to-r from-blue-600 to-sky-500 hover:from-blue-700 hover:to-sky-600 text-white text-sm font-bold rounded-2xl shadow-lg shadow-blue-500/10 hover:shadow-blue-500/20 transition-all duration-300">
                    <span>GET /api/user</span>
                    <svg class="w-4 h-4 transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"></path>
                    </svg>
                </a>
            </div>

        </div>

        <!-- Form Feedback (Validasi Input) -->
        <div class="bg-white/70 backdrop-blur-xl border border-white/80 rounded-3xl p-8 shadow-xl shadow-blue-100/30">
            <div class="flex items-center gap-3 mb-3">
                <div class="p-2.5 rounded-xl bg-indigo-50 text-indigo-600 border border-indigo-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21.75l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z"></path>
                    </svg>
                </div>
                <h2 class="text-lg font-bold text-slate-800">Formulir Masukan (Validasi Input)</h2>
            </div>
            <p class="text-sm text-slate-500 mb-6 font-medium leading-relaxed">
                Formulir ini menggunakan validasi server-side yang ketat untuk mendemonstrasikan proteksi terhadap kerentanan XSS dan SQL Injection.
            </p>

            @if(session('success_feedback'))
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-100 text-emerald-600 text-sm font-medium rounded-2xl shadow-sm flex gap-2 animate-fade-in">
                    <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>{{ session('success_feedback') }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('dashboard.submit') }}" class="space-y-6">
                @csrf

                <div>
                    <label for="name" class="block text-sm font-bold text-slate-700 mb-1.5">Nama Lengkap</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}"
                           class="w-full px-4 py-3 bg-white border border-slate-200 rounded-2xl text-slate-800 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all @error('name') border-rose-400 focus:ring-rose-400/20 focus:border-rose-400 @enderror"
                           placeholder="Masukkan nama lengkap (min. 3 karakter)">
                    @error('name')
                        <p class="mt-1.5 text-xs text-rose-500 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-bold text-slate-700 mb-1.5">Alamat Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                           class="w-full px-4 py-3 bg-white border border-slate-200 rounded-2xl text-slate-800 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all @error('email') border-rose-400 focus:ring-rose-400/20 focus:border-rose-400 @enderror"
                           placeholder="Masukkan email valid">
                    @error('email')
                        <p class="mt-1.5 text-xs text-rose-500 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="feedback" class="block text-sm font-bold text-slate-700 mb-1.5">Pesan Masukan / Feedback</label>
                    <textarea id="feedback" name="feedback" rows="4"
                              class="w-full px-4 py-3 bg-white border border-slate-200 rounded-2xl text-slate-800 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all @error('feedback') border-rose-400 focus:ring-rose-400/20 focus:border-rose-400 @enderror"
                              placeholder="Tulis masukan Anda (min. 10 karakter)">{{ old('feedback') }}</textarea>
                    @error('feedback')
                        <p class="mt-1.5 text-xs text-rose-500 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                        class="px-8 py-3.5 bg-gradient-to-r from-blue-600 to-sky-500 hover:from-blue-700 hover:to-sky-600 text-white text-sm font-bold rounded-2xl shadow-lg shadow-blue-500/10 hover:shadow-blue-500/25 transition-all duration-300 transform hover:-translate-y-0.5">
                    Kirim Feedback
                </button>
            </form>
        </div>

    </main>

    <!-- Footer -->
    <footer class="relative z-10 text-center py-8 text-xs text-slate-400 font-semibold border-t border-slate-200/60 mt-10">
        &copy; 2026 Secure App - Dibuat oleh Muhammad Mardiansyah
    </footer>

</body>
</html>
