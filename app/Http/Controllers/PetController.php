<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PetController extends Controller
{
    private function loadPets()
    {
        if (!Storage::exists('pets.json')) {
            return [];
        }

        return json_decode(Storage::get('pets.json'), true);
    }

    private function savePets($pets)
    {
        Storage::put('pets.json', json_encode($pets, JSON_PRETTY_PRINT));
    }

    public function getByOwner($email)
    {
        $pets = $this->loadPets();
        $userPets = array_filter($pets, fn($pet) => $pet['owner'] === $email);

        return response()->json(array_values($userPets));
    }

    public function store(Request $request)
    {
        $data = $request->only(['name', 'type', 'gender', 'age', 'owner']);

        // Validasi sederhana
        foreach (['name', 'type', 'gender', 'age', 'owner'] as $field) {
            if (empty($data[$field])) {
                return response()->json(['error' => "$field is required"], 400);
            }
        }

        $pets = $this->loadPets();
        $data['id'] = uniqid(); // kasih ID unik
        $pets[] = $data;

        $this->savePets($pets);

        return response()->json(['message' => 'Pet added successfully', 'pet' => $data], 201);
    }
}
