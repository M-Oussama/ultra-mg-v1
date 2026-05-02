<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

            // Eager load native role and array of capabilities so it serializes properly into JSON
            $user->load('role.permissions');

            // Manual token creation bypass to handle the buggy production 'tokenable' column
            $plainTextToken = Str::random(40);
            $tokenInstance = $user->tokens()->create([
                'name' => Str::random(80),
                'token' => hash('sha256', $plainTextToken),
                'abilities' => ['*'],
                'tokenable' => 'legacy_bypass', // This satisfies the NOT NULL constraint on production
            ]);
            $token = $tokenInstance->id . '|' . $plainTextToken;

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
