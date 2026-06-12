<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $clientId = env('WORKOS_CLIENT_ID');
        $redirectUri = env('WORKOS_REDIRECT_URI');

        // Gunakan screen_hint=sign-in yang didukung secara resmi oleh WorkOS AuthKit
        $query = http_build_query([
            'client_id'     => $clientId,
            'redirect_uri'  => $redirectUri,
            'response_type' => 'code',
            'provider'      => 'authkit',
            'screen_hint'   => 'sign-in',
        ]);

        return redirect('https://api.workos.com/user_management/authorize?' . $query);
    }
    
    /**
     * Handle the WorkOS OAuth callback.
     */
    public function callback(Request $request)
    {
        $code = $request->query('code');

        if (!$code) {
            return redirect()->route('login')->with('error', 'Authentication code not provided.');
        }

        $clientId = env('WORKOS_CLIENT_ID');
        $apiKey = env('WORKOS_API_KEY');

        try {
            // Real WorkOS Code Exchange
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post('https://api.workos.com/user_management/authenticate', [
                'client_id' => $clientId,
                'client_secret' => $apiKey,
                'code' => $code,
                'grant_type' => 'authorization_code',
            ]);

            if ($response->failed()) {
                Log::error('WorkOS code exchange failed', ['response' => $response->body()]);
                return redirect()->route('login')->with('error', 'Authentication failed. Please verify your WorkOS credentials.');
            }

            $data = $response->json();
            Log::info('WorkOS auth response:', $data);
            $workosUser = $data['user'] ?? null;
            
            // Extract session_id (sid) from JWT Access Token
            $sessionId = null;
            if (!empty($data['access_token'])) {
                $parts = explode('.', $data['access_token']);
                if (count($parts) === 3) {
                    $payload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $parts[1])), true);
                    $sessionId = $payload['sid'] ?? null;
                }
            }
            if (!$sessionId) {
                $sessionId = $data['session_id'] ?? ($data['session']['id'] ?? null);
            }

            if (!$workosUser || empty($workosUser['email'])) {
                return redirect()->route('login')->with('error', 'Failed to retrieve email from WorkOS.');
            }

            $name = trim(($workosUser['first_name'] ?? '') . ' ' . ($workosUser['last_name'] ?? 'User'));
            if (empty($name)) {
                $name = 'WorkOS User';
            }

            $user = User::firstOrCreate(
                ['email' => $workosUser['email']],
                [
                    'name' => $name,
                    'password' => bcrypt(Str::random(16)),
                ]
            );

            Auth::login($user);
            
            // Simpan session_id WorkOS ke session Laravel
            if ($sessionId) {
                $request->session()->put('workos_session_id', $sessionId);
            }

            return redirect()->route('dashboard')->with('success', 'Logged in successfully via WorkOS!');
        } catch (\Exception $e) {
            Log::error('WorkOS Exception', ['message' => $e->getMessage()]);
            return redirect()->route('login')->with('error', 'An unexpected error occurred during login.');
        }
    }

    /**
     * Log out from session.
     */
    public function logout(Request $request)
    {
        $workosSessionId = $request->session()->get('workos_session_id');

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Jika ada session_id WorkOS, redirect ke URL logout WorkOS untuk menghapus cookie di sisi WorkOS
        if ($workosSessionId) {
            $query = http_build_query([
                'session_id' => $workosSessionId,
            ]);
            return redirect('https://api.workos.com/user_management/sessions/logout?' . $query);
        }

        return redirect()->route('login.page')->with('success', 'Anda berhasil keluar.');
    }
}
