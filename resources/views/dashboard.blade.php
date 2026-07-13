<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Aplikasi Secure</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen font-sans selection:bg-indigo-500 selection:text-white">

    <div class="absolute top-0 left-1/4 w-96 h-96 bg-indigo-600/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-purple-600/10 rounded-full blur-3xl pointer-events-none"></div>

    <nav class="relative z-10 flex items-center justify-between px-6 py-4 border-b border-slate-800/60 bg-slate-900/40 backdrop-blur-md">
        <h1 class="text-lg font-bold bg-gradient-to-r from-indigo-400 to-purple-400 bg-clip-text text-transparent">
            SecureApp
        </h1>
        <div class="flex items-center gap-4">
            <span class="text-sm text-slate-400">{{ Auth::user()->name }}</span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-sm text-rose-400 hover:text-rose-300 transition-colors">
                    Logout
                </button>
            </form>
        </div>
    </nav>

    <main class="relative z-10 max-w-4xl mx-auto px-4 py-10 space-y-8">

        @if(session('success'))
            <div class="p-4 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-sm rounded-xl">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <div class="bg-slate-900/60 backdrop-blur-xl border border-slate-800 rounded-2xl p-6">
                <h2 class="text-lg font-semibold text-slate-200 mb-4">Profil Pengguna</h2>
                <div class="space-y-3 text-sm">
                    <div>
                        <span class="text-slate-500">ID:</span>
                        <span class="text-slate-300 ml-2">{{ Auth::id() }}</span>
                    </div>
                    <div>
                        <span class="text-slate-500">Nama:</span>
                        <span class="text-slate-300 ml-2">{{ Auth::user()->name }}</span>
                    </div>
                    <div>
                        <span class="text-slate-500">Email:</span>
                        <span class="text-slate-300 ml-2">{{ Auth::user()->email }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-slate-900/60 backdrop-blur-xl border border-slate-800 rounded-2xl p-6">
                <h2 class="text-lg font-semibold text-slate-200 mb-4">API Endpoint</h2>
                <p class="text-sm text-slate-400 mb-3">Akses data profil dalam format JSON:</p>
                <a href="{{ route('api.user') }}" target="_blank"
                   class="inline-block px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                    GET /api/user
                </a>
            </div>

        </div>

        <div class="bg-slate-900/60 backdrop-blur-xl border border-slate-800 rounded-2xl p-6">
            <h2 class="text-lg font-semibold text-slate-200 mb-4">Form Feedback (Validasi Input)</h2>
            <p class="text-sm text-slate-400 mb-4">Formulir ini mendemonstrasikan validasi input server-side untuk mencegah XSS & SQL Injection.</p>

            @if(session('success_feedback'))
                <div class="mb-4 p-3 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-sm rounded-xl">
                    {{ session('success_feedback') }}
                </div>
            @endif

            <form method="POST" action="{{ route('dashboard.submit') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="name" class="block text-sm text-slate-400 mb-1">Nama</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}"
                           class="w-full px-4 py-2 bg-slate-800/60 border border-slate-700 rounded-lg text-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('name') border-rose-500 @enderror"
                           placeholder="Masukkan nama (min. 3 karakter)">
                    @error('name')
                        <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm text-slate-400 mb-1">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                           class="w-full px-4 py-2 bg-slate-800/60 border border-slate-700 rounded-lg text-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('email') border-rose-500 @enderror"
                           placeholder="Masukkan email valid">
                    @error('email')
                        <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="feedback" class="block text-sm text-slate-400 mb-1">Feedback</label>
                    <textarea id="feedback" name="feedback" rows="4"
                              class="w-full px-4 py-2 bg-slate-800/60 border border-slate-700 rounded-lg text-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('feedback') border-rose-500 @enderror"
                              placeholder="Tulis feedback Anda (min. 10 karakter)">{{ old('feedback') }}</textarea>
                    @error('feedback')
                        <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                        class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow-lg shadow-indigo-600/20 transition-all duration-300">
                    Kirim Feedback
                </button>
            </form>
        </div>

    </main>

    <footer class="relative z-10 text-center py-6 text-xs text-slate-600 border-t border-slate-800/40 mt-10">
        &copy; 2026 Aplikasi Secure Terproteksi - Dibuat oleh Muhammad Mardiansyah
    </footer>

</body>

</html>
