<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class RegisterController extends Controller
{
    // private static $accounts = [];

    private function loadAccounts()
    {
        if (!Storage::exists('accounts.json')) {
            return [];
        }

        return json_decode(Storage::get('accounts.json'), true);
    }

    private function saveAccounts($accounts)
    {
        Storage::put('accounts.json', json_encode($accounts, JSON_PRETTY_PRINT));
    }

    public function register(Request $request)
    {
        // CORS headers
        // header("Access-Control-Allow-Origin: *");
        // header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
        // header("Access-Control-Allow-Headers: Content-Type");

        if ($request->isMethod('options')) {
            return response()->noContent();
        }

        if (!$request->isMethod('post')) {
            return response()->json(['error' => 'Method not allowed'], 405);
        }

        $data = $request->only(['name', 'email', 'password']);

        if (empty($data['email']) || empty($data['password'])) {
            return response()->json(['error' => 'Invalid request'], 400);
        }

        Log::info("New user registered: {$data['name']} <{$data['email']}>");

        // self::$accounts[] = $data;
        // session()->push('accounts', $data);
        $accounts = $this->loadAccounts();
        $accounts[] = $data;
        $this->saveAccounts($accounts);

        return response()->json(['message' => 'Register success, Sip'], 201);
    }

    public function acc(Request $request)
    {
        if (!$request->isMethod('get')) {
            return response()->json(['error' => 'Method not allowed'], 405);
        }
        // $accounts = session('accounts', []);
        // return response()->json($accounts, 200, [], JSON_PRETTY_PRINT);
        $accounts = $this->loadAccounts();
        return response()->json($accounts, 200, [], JSON_PRETTY_PRINT);
    }

    public function home(Request $request)
    {
        if (!$request->isMethod('get')) {
            return response()->json(['error' => 'Method not allowed'], 405);
        }

        return response()->json(['message' => 'Hai :v']);
    }
}
