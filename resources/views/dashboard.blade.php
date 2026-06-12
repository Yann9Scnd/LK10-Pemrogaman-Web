<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Aplikasi Secure Ceo Adyatma86</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-950 text-slate-100 min-h-screen font-sans relative">

    <div class="absolute top-0 right-0 w-96 h-96 bg-indigo-600/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-0 left-0 w-96 h-96 bg-purple-600/10 rounded-full blur-3xl pointer-events-none"></div>

    <header class="border-b border-slate-800 bg-slate-900/45 backdrop-blur-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="w-3.5 h-3.5 rounded-full bg-emerald-500 shadow-lg shadow-emerald-500/50"></span>
                <span
                    class="font-bold tracking-wider text-lg bg-gradient-to-r from-indigo-400 to-purple-400 bg-clip-text text-transparent">GaranganBaik</span>
            </div>

            <div class="flex items-center gap-4">
                <span class="text-sm text-slate-400 hidden sm:inline">Halo, <strong
                        class="text-slate-200">{{ Auth::user()->name }}</strong></span>
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit"
                        class="px-4 py-2 text-xs font-semibold text-rose-400 hover:text-white bg-rose-500/10 hover:bg-rose-500/80 border border-rose-500/20 rounded-lg transition-all duration-300">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-6 backdrop-blur-sm h-fit">
                <h2 class="text-lg font-bold text-slate-200 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z">
                        </path>
                    </svg>
                    Informasi Sesi Pengguna
                </h2>
                <div class="space-y-3 text-sm">
                    <div class="pb-2 border-b border-slate-850">
                        <span class="text-xs text-slate-500 block">Nama Lengkap</span>
                        <span class="font-medium text-slate-300">{{ Auth::user()->name }}</span>
                    </div>
                    <div class="pb-2 border-b border-slate-850">
                        <span class="text-xs text-slate-500 block">Email Terdaftar</span>
                        <span class="font-medium text-slate-300">{{ Auth::user()->email }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-slate-500 block">ID Pengguna</span>
                        <span
                            class="font-mono text-xs text-slate-400 bg-slate-950 px-2 py-1 rounded inline-block mt-1">{{ Auth::id() }}</span>
                    </div>
                </div>
            </div>

            <div
                class="bg-slate-900/60 border border-slate-800 rounded-2xl p-6 backdrop-blur-sm lg:col-span-2 space-y-6">
                <div>
                    <h2 class="text-xl font-bold text-slate-200">Formulir Masukan (Validasi & Sanitasi)</h2>
                    <p class="text-sm text-slate-400 mt-1">Mengilustrasikan pengamanan input dari serangan XSS dan SQLi
                        secara langsung.</p>
                </div>

                @if (session('success_feedback'))
                    <div class="p-4 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-sm rounded-xl">
                        {{ session('success_feedback') }}
                    </div>
                @endif

                <form action="{{ route('dashboard.submit') }}" method="POST" class="space-y-4">
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-400 mb-1" for="name">Nama (min: 3,
                                max: 50)</label>
                            <input type="text" id="name" name="name"
                                value="{{ old('name', Auth::user()->name) }}"
                                class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl focus:outline-none focus:border-indigo-500 text-sm transition-all @error('name') border-rose-500 @enderror">
                            @error('name')
                                <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-400 mb-1" for="email">Alamat
                                Email</label>
                            <input type="email" id="email" name="email"
                                value="{{ old('email', Auth::user()->email) }}"
                                class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl focus:outline-none focus:border-indigo-500 text-sm transition-all @error('email') border-rose-500 @enderror">
                            @error('email')
                                <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-400 mb-1" for="feedback">Pesan
                            Feedback</label>
                        <textarea id="feedback" name="feedback" rows="4" placeholder="Tuliskan feedback Anda disini..."
                            class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl focus:outline-none focus:border-indigo-500 text-sm transition-all @error('feedback') border-rose-500 @enderror">{{ old('feedback') }}</textarea>
                        @error('feedback')
                            <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit"
                        class="py-2.5 px-6 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-xl transition-all duration-300">
                        Kirim Feedback Secara Aman
                    </button>
                </form>
            </div>

            <div
                class="bg-slate-900/60 border border-slate-800 rounded-2xl p-6 backdrop-blur-sm lg:col-span-3 space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-slate-200">Integrasi Endpoint API (JSON)</h2>
                        <p class="text-sm text-slate-400">Endpoint terproteksi di <code
                                class="text-indigo-400">/api/user</code> yang mengembalikan data JSON profil.</p>
                    </div>
                    <button id="btn-fetch-api"
                        class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 rounded-xl text-xs font-semibold transition-all">
                        Ambil Data API
                    </button>
                </div>

                <div class="bg-slate-950 p-4 rounded-xl border border-slate-850 relative">
                    <span
                        class="absolute right-3 top-3 text-[10px] uppercase font-mono tracking-widest text-slate-600">Response</span>
                    <pre id="api-output" class="text-xs font-mono text-emerald-400 overflow-x-auto min-h-[40px] flex items-center">Menunggu aksi Anda... Klik tombol "Ambil Data API" di atas.</pre>
                </div>
            </div>

        </div>
    </main>

    <footer
        class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 border-t border-slate-900 text-center text-xs text-slate-600">
        &copy; 2026 Aplikasi Secure Terproteksi - Dibuat khusus untuk Ceo Adyatma86
    </footer>

</body>

</html>
