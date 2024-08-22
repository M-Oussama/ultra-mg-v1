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

           $token = $user->createToken(Str::random(80))->accessToken;


           return response()->json([
               'accessToken' => $token->token,
               'token_type' => 'Bearer',
               'userData' => $user,
               'userAbilities' => $user->role->permissions

           ]);
       }

       return response()->json(['error' => 'The e-mail address or password is incorrect.'], 401);


   }
}
