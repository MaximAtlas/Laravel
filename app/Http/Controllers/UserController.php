<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\LoginRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

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

        $user = Auth::guard('web')->user();
        $token = Str::random(60);

        $user->update(['api_token' => $token]);

        return ['token' => $token];

    }
}
