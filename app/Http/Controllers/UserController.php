<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\LoginRequest;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function login(LoginRequest $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');

        if (! Auth::guard('web')->attempt(['email' => $email, 'password' => $password])) {
            return response()->json([
                'message' => 'Неверный логин или пароль',
            ], 401);

        }

        $user = Auth::guard('api')->user();
        $user->tokens()->delete();

        $token = $user->createToken('auth_token')->plainTextToken;
        //dd($user->update(['api_token' => $token]));

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);

    }
}
