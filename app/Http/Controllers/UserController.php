<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function register(UserRegisterRequest $request) {
        $validated = $request->validated();

        $user = User::create($validated);

        return response()->json([
            'message' => ['User created successfully'],
            'user' => $user,
        ], 201);
    }
}
