<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function Login(Request $request) {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $user->load('role.permissions');

            // Standard Sanctum token creation (works once 'tokenable' column is nullable)
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'accessToken' => $token,
                'token_type' => 'Bearer',
                'business_id' => $user->organization_id ?? 1,
                'userData' => $user,
                'userAbilities' => $user->role ? $user->role->permissions : []
            ]);
        }

        return response()->json(['error' => 'The e-mail address or password is incorrect.'], 401);
    }
}
