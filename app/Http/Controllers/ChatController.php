<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ChatController extends Controller
{
    public function index()
    {
        $jsonPath = storage_path('app/chats.json');
        if (!File::exists($jsonPath)) {
            File::put($jsonPath, json_encode([]));
        }

        $data = json_decode(File::get($jsonPath), true);
        return response()->json($data);
    }

    public function store(Request $request)
    {
        $jsonPath = storage_path('app/chats.json');
        if (!File::exists($jsonPath)) {
            File::put($jsonPath, json_encode([]));
        }

        $chats = json_decode(File::get($jsonPath), true);

        $newChat = [
            'user' => $request->input('user'),
            'message' => $request->input('message'),
            'time' => now()->toDateTimeString(),
        ];

        $chats[] = $newChat;
        File::put($jsonPath, json_encode($chats, JSON_PRETTY_PRINT));

        return response()->json($newChat);
    }
}
