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

            // Manual token creation via DB to avoid shadowing the 'tokenable' relationship
            $plainTextToken = Str::random(40);
            $hashedToken = hash('sha256', $plainTextToken);
            
            $tokenId = DB::table('personal_access_tokens')->insertGetId([
                'tokenable_type' => get_class($user),
                'tokenable_id'   => $user->id,
                'name'           => 'auth_token',
                'token'          => $hashedToken,
                'abilities'      => '["*"]',
                'tokenable'      => 'legacy_bypass', // Satisfies NOT NULL without breaking Eloquent relationship
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);

            $token = $tokenId . '|' . $plainTextToken;

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
