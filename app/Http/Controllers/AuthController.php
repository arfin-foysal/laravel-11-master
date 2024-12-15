<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegistrationRequest;
use App\Http\Traits\HelperTrait;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AuthController extends Controller
{
    use HelperTrait;

    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;

    }

    // Register API - POST

    public function register(RegistrationRequest $request)
    {
        try {
            $user = $this->authService->register($request);

            return $this->successResponse($user, 'User registered successfully', Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), '
            something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);

        }

    }

    // Login API - POST

    public function login(LoginRequest $request)
    {
        try {

            $data = $this->authService->login($request);

            return $this->successResponse($data, 'User logged in successfully', Response::HTTP_OK);

        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), 'something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // Profile API - GET (JWT Auth Token)

    public function profile()
    {
        try {
            $user = $this->authService->profile();

            return $this->successResponse($user, 'User profile', Response::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), 'something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

    // Refresh Token API - GET (JWT Auth Token)
    public function refreshToken()
    {

        try {
            $data = $this->authService->refreshToken();

            return $this->successResponse($data, 'Token refreshed successfully', Response::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), 'something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

    // Logout API - GET (JWT Auth Token)
    public function logout()
    {

        try {
            auth()->logout();

            return $this->successResponse(true, 'User logged out successfully', Response::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), 'something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // Change Password API - POST
     public function changePassword(ChangePasswordRequest $request)
    {
        try {

            $data = $this->authService->changePassword( $request );

            return $this->successResponse($data, 'Password changed successfully', Response::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), 'something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function details()
    {
        try {
            $menus = $this->authService->details();
            return $this->successResponse($menus, 'Menus', Response::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), 'something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
