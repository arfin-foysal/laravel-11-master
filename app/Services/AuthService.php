<?php

namespace App\Services;

use App\Http\Traits\HelperTrait;
use App\Models\Menu;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthService
{
    use HelperTrait;

    public function __construct()
    {
        //
    }

    public function register($request)
    {
        try {
            $path = $this->fileUpload($request, 'image', 'users');
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'username' => $request->username,
                'number' => $request->number,
                'image' => $path,
                'is_active' => 0,
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

            // Attempt authentication with email first
            $credentials = ['email' => $request->email_or_username, 'password' => $request->password];
            $token = auth()->attempt($credentials);

            // If email authentication fails, attempt with username
            if (! $token) {
                $credentials = ['username' => $request->email_or_username, 'password' => $request->password];
                $token = auth()->attempt($credentials);
            }

            // Throw an exception if both attempts fail
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


    public function changePassword($request)
    {

        try {
            $user = auth()->user();
            $user->password = bcrypt($request->password);
            $user->save();
            return $user;
        } catch (\Throwable $th) {
            throw $th;
        }
    }


    public function details()
    {
        try {
            $user = auth()->user();
            $role = $user->roles()->first()->name ?? null;
            $permissions = $user->getAllPermissions()->pluck('name');
            $extraPermissions = $user->getDirectPermissions()->pluck('name');
            $rolePermissions = $user->getPermissionsViaRoles()->pluck('name');

            // Retrieve only active menus with their associated active submenus, ordered by 'order'
            $menus = Menu::whereHas('roles', function ($query) use ($user) {
                $query->whereIn('roles.id', $user->roles->pluck('id'));
            })
                ->select('id', 'name', 'description', 'url', 'icon', 'order', 'is_active')
                ->where('is_active', true) // Filter by active menus
                ->orderBy('order') // Order by 'order' column
                ->with(['subMenus' => function ($query) {
                    $query->select('id', 'menu_id', 'name', 'description', 'icon', 'url', 'order', 'is_active')
                        ->where('is_active', true) // Filter by active submenus
                        ->orderBy('order'); // Order submenus by 'order' column
                }])
                ->get();

            return [
                'role' => $role,
                'permissions' => $permissions,
                'role_permissions' => $rolePermissions,
                'extra_permissions' => $extraPermissions,
                'menus' => $menus
            ];
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
