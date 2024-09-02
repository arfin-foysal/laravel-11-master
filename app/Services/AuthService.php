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

            $user = User::where('id', auth()->id())->first();
            $role = $user->roles()->first()->name ?? null;
            $permissions = $user->getAllPermissions()->pluck('name');
            $extraPermissions = $user->getDirectPermissions()->pluck('name');
            $rolePermissions = $user->getPermissionsViaRoles()->pluck('name');
            $expiresIn = auth()->factory()->getTTL() * 60;

            return [
                'token_type' => 'bearer',
                'token' => $token,
                'expires_in' => $expiresIn,
                'role' => $role,
                'permissions' => $permissions,
                'role_permissions' => $rolePermissions,
                'extra_permissions' => $extraPermissions,
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
            $expiresIn = auth()->factory()->getTTL() * 60;

            return [
                'token_type' => 'bearer',
                'token' => $token,
                'expires_in' => $expiresIn,
            ];
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
