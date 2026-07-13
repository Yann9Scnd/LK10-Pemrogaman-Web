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
    /**
     * Redirect users to the WorkOS AuthKit authorize URL.
     */
    public function login(Request $request)
    {
        $clientId = config('services.workos.client_id');
        $redirectUri = config('services.workos.redirect_uri');

        // SECURITY FIX (Bug 3): Generate cryptographically secure state token to prevent OAuth CSRF
        $state = Str::random(40);
        $request->session()->put('oauth_state', $state);

        $query = http_build_query([
            'client_id'     => $clientId,
            'redirect_uri'  => $redirectUri,
            'response_type' => 'code',
            'provider'      => 'authkit',
            'screen_hint'   => 'sign-in',
            'state'         => $state, // Pass the state to WorkOS
        ]);

        return redirect('https://api.workos.com/user_management/authorize?' . $query);
    }
    
    /**
     * Handle the WorkOS OAuth callback.
     */
    public function callback(Request $request)
    {
        $code = $request->query('code');
        $state = $request->query('state');
        $savedState = $request->session()->pull('oauth_state'); // Retrieve and clear from session

        // SECURITY FIX (Bug 3): Validate the state parameter
        if (!$state || !$savedState || $state !== $savedState) {
            return redirect()->route('login.page')->with('error', 'Invalid state token. Possible CSRF attack detected.');
        }

        // BUG FIX (Bug 1): Redirect to login.page instead of login (which causes loop redirect)
        if (!$code) {
            return redirect()->route('login.page')->with('error', 'Authentication code not provided.');
        }

        // ARCHITECTURAL REFACTOR (Bug 2): Use config() instead of direct env() calls
        $clientId = config('services.workos.client_id');
        $apiKey = config('services.workos.api_key');

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
                return redirect()->route('login.page')->with('error', 'Authentication failed. Please verify your WorkOS credentials.');
            }

            $data = $response->json();
            Log::info('WorkOS auth response:', $data);
            $workosUser = $data['user'] ?? null;
            
            // CODE REFACTOR (Refactoring 1): Extract JWT Access Token payload decoding into a private helper method
            $sessionId = null;
            if (!empty($data['access_token'])) {
                $payload = $this->decodeJwtPayload($data['access_token']);
                $sessionId = $payload['sid'] ?? null;
            }
            if (!$sessionId) {
                $sessionId = $data['session_id'] ?? ($data['session']['id'] ?? null);
            }

            if (!$workosUser || empty($workosUser['email'])) {
                return redirect()->route('login.page')->with('error', 'Failed to retrieve email from WorkOS.');
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
            
            // Store WorkOS session_id to Laravel session for logout
            if ($sessionId) {
                $request->session()->put('workos_session_id', $sessionId);
            }

            return redirect()->route('dashboard')->with('success', 'Logged in successfully via WorkOS!');
        } catch (\Exception $e) {
            Log::error('WorkOS Exception', ['message' => $e->getMessage()]);
            return redirect()->route('login.page')->with('error', 'An unexpected error occurred during login.');
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

        // If there's a WorkOS session ID, redirect to WorkOS logout endpoint to clear cookies on their end
        if ($workosSessionId) {
            $query = http_build_query([
                'session_id' => $workosSessionId,
            ]);
            return redirect('https://api.workos.com/user_management/sessions/logout?' . $query);
        }

        return redirect()->route('login.page')->with('success', 'Anda berhasil keluar.');
    }

    /**
     * CODE REFACTOR (Refactoring 1): Helper to decode JWT token payload safely.
     */
    private function decodeJwtPayload(string $token): ?array
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return null;
        }

        // Base64URL decoding helper
        $base64 = strtr($parts[1], '-_', '+/');
        $padded = str_pad($base64, strlen($base64) % 4, '=', STR_PAD_RIGHT);
        $decoded = base64_decode($padded);

        return $decoded ? json_decode($decoded, true) : null;
    }
}
