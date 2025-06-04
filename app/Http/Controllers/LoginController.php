<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class LoginController extends Controller
{
    private function loadAccounts()
    {
        if (!Storage::exists('accounts.json')) {
            return [];
        }

        return json_decode(Storage::get('accounts.json'), true);
    }

    private function generateJWT($user)
    {
        $payload = [
            'iss' => 'purrfectmate',
            'sub' => $user['email'],
            'email' => $user['email'],
            'name' => $user['name'],
            'iat' => time(),
            'exp' => time() + (60 * 60 * 24), // 24 jam
        ];

        // $secret = env('JWT_SECRET');
        $secret = env('JWT_SECRET', 'fallback_key');
        return JWT::encode($payload, $secret, 'HS256');
    }

    public function login(Request $request)
    {
        if ($request->isMethod('options')) {
            return response()->noContent();
        }

        if (!$request->isMethod('post')) {
            return response()->json(['error' => 'Method not allowed'], 405);
        }

        $data = $request->only(['email', 'password']);

        if (empty($data['email']) || empty($data['password'])) {
            return response()->json(['error' => 'Email dan password wajib diisi'], 400);
        }

        $accounts = $this->loadAccounts();
        $user = collect($accounts)->firstWhere('email', $data['email']);

        if (!$user || $user['password'] !== $data['password']) {
            return response()->json(['error' => 'Email atau password salah'], 401);
        }

        $token = $this->generateJWT($user);

        Log::info("Login sukses: {$user['email']}");

        return response()->json([
            'message' => 'Login sukses',
            'token' => $token,
            'user' => [
                'name' => $user['name'],
                'email' => $user['email'],
            ]
        ]);
    }

   
}
