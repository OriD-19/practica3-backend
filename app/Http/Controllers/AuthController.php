<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(LoginRequest $request) {
        $validated = $request->validated();

        if (Auth::attempt($request->only('email', 'password'))) {
            /** @var \App\Models\User */
            $user = Auth::user();
            $token = $user->createToken('token-name');

            return response()->json([
                'status' => 'success',
                'message' => 'Login successful',
                'token' => $token->plainTextToken,
                'user' => Auth::user(),
            ]);
        }

        return response()->json([
            'message' => 'Email or password incorrect',
        ]);
    }
}
