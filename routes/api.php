<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PetController;
use App\Http\Controllers\ChatController;


use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;


Route::match(['post', 'options'], '/register', [RegisterController::class, 'register'])->middleware('api');
Route::get('/acc', [RegisterController::class, 'acc']);
Route::get('/home', [RegisterController::class, 'home']);

Route::post('/login', [LoginController::class, 'login']);
// Route::get('/me', [LoginController::class, 'me']);
// Route::middleware('jwt.auth')->get('/me', [LoginController::class, 'me']);

Route::get('/me', function (Request $request) {
    try {
        $authHeader = $request->header('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $token = substr($authHeader, 7);
        $secret = env('JWT_SECRET');

        $decoded = JWT::decode($token, new Key($secret, 'HS256'));

        return response()->json([
            'email' => $decoded->email,
            'name' => $decoded->name ?? 'Guest',
        ]);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Invalid token', 'message' => $e->getMessage()], 401);
    }
});
Route::get('/pat/{email}', [PetController::class, 'getByOwner']);
Route::post('/pat', [PetController::class, 'store']);

Route::get('/chats', [ChatController::class, 'index']);
Route::post('/chats', [ChatController::class, 'store']);
