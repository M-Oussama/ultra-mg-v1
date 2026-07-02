<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
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
            $user->load('role.permissions', 'organization');

            // Standard Sanctum token creation.
            // Some production databases still have the legacy NOT NULL tokenable column,
            // so we self-heal it once and retry before failing the login.
            try {
                $token = $user->createToken('auth_token')->plainTextToken;
            } catch (\Throwable $e) {
                if ($this->fixLegacySanctumTokenColumn()) {
                    $token = $user->createToken('auth_token')->plainTextToken;
                } else {
                    throw $e;
                }
            }

            return response()->json([
                'accessToken' => $token,
                'token_type' => 'Bearer',
                'business_id' => $user->organization_id ?? 1,
                'business_name' => $user->organization?->name ?? 'Business',
                'userData' => $user,
                'userAbilities' => $user->role ? $user->role->permissions : []
            ]);
        }

        return response()->json(['error' => 'The e-mail address or password is incorrect.'], 401);
    }

    private function fixLegacySanctumTokenColumn(): bool
    {
        try {
            if (DB::getDriverName() === 'sqlite') {
                return false;
            }

            if (!Schema::hasTable('personal_access_tokens')) {
                return false;
            }

            if (!Schema::hasColumn('personal_access_tokens', 'tokenable')) {
                return false;
            }

            DB::statement(
                'ALTER TABLE personal_access_tokens CHANGE `tokenable` `tokenable_legacy` VARCHAR(255) NULL DEFAULT NULL'
            );

            return true;
        } catch (\Throwable $e) {
            logger()->warning('Failed to auto-fix Sanctum tokenable column during login.', [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}
