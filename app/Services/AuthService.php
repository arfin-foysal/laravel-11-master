<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthService
{
    public function __construct()
    {
        //
    }

    public function register($request)
    {
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);

            return $user;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function login($request)
    {

        try {

            // Auth Facade
            // $token = Auth::attempt([
            //     "email" => $request->email,
            //     "password" => $request->password
            // ]);

            $token = auth()->attempt([
                'email' => $request->email,
                'password' => $request->password,
            ]);

            if (! $token) {
                throw new \Exception('Invalid credentials');
            }

            return [
                'token_type' => 'bearer',
                'token' => $token,
                'expires_in' => auth()->factory()->getTTL() * 60,
                'user' => auth()->user(),
            ];

        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function profile()
    {
        try {
            $userData = auth()->user();
            //  $userData = request()->user();

            return [
                'user' => $userData,
                'user_id' => auth()->user()->id,
                'email' => auth()->user()->email,
                // "user_id" => request()->user()->id,
                // "email" => request()->user()->email
            ];

        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function refreshToken()
    {
        try {
            $token = auth()->refresh();

            return [
                'token_type' => 'bearer',
                'token' => $token,
                'expires_in' => auth()->factory()->getTTL() * 60,
            ];
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
