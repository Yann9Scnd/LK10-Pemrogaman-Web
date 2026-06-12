<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Redirect root to dashboard or login page view
Route::get('/', function () {
    return Auth::check() ? redirect()->route('dashboard') : redirect()->route('login.page');
});

// Authentication Routes
// Menampilkan halaman login (tampilan, tidak auto-redirect ke WorkOS)
Route::get('/login-page', function () {
    return view('login');
})->name('login.page');

// Redirect langsung ke WorkOS AuthKit
Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::get('/auth/callback', [AuthController::class, 'callback'])->name('auth.callback');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes (Hanya untuk user yang login)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/submit', [DashboardController::class, 'submitForm'])->name('dashboard.submit');

    // 1 Endpoint API sederhana (JSON)
    Route::get('/api/user', function () {
        return response()->json([
            'success' => true,
            'message' => 'Detail profil user yang sedang login',
            'data' => [
                'id' => Auth::id(),
                'name' => Auth::user()->name,
                'email' => Auth::user()->email,
            ]
        ]);
    })->name('api.user');
});

