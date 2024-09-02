<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssignRolePermissionRequest;
use App\Http\Requests\AssignUserRoleRequest;
use App\Http\Requests\RemoveRolePermissionRequest;
use App\Http\Requests\RemoveUserRoleRequest;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Http\Traits\HelperTrait;
use App\Services\RoleService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class RoleController extends Controller
{
    use HelperTrait;

    private $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    public function index(Request $request)
    {

        try {
            $roles = $this->roleService->index($request);

            return $this->successResponse($roles, 'Roles retrieved successfully', Response::HTTP_OK);

        } catch (\Throwable $th) {
            return $this->errorResponse([], $th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(StoreRoleRequest $request)
    {

        try {

            $role = $this->roleService->create($request);

            return $this->successResponse($role, 'Role created successfully', Response::HTTP_CREATED);
        } catch (ValidationException $e) {

            return $this->errorResponse($e->errors(), $e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Throwable $th) {

            return $this->errorResponse([], $th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id)
    {
        try {
            $role = $this->roleService->show($id);

            return $this->successResponse($role, 'Role retrieved successfully', Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {

            return $this->errorResponse([], $e->getMessage(), Response::HTTP_NOT_FOUND);
        } catch (\Throwable $th) {

            return $this->errorResponse([], $th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(UpdateRoleRequest $request, $id)
    {

        try {
            $role = $this->roleService->update($request, $id);

            return $this->successResponse($role, 'Role updated successfully', Response::HTTP_OK);
        } catch (ValidationException $e) {

            return $this->errorResponse($e->errors(), $e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (ModelNotFoundException $e) {

            return $this->errorResponse([], $e->getMessage(), Response::HTTP_NOT_FOUND);
        } catch (\Throwable $th) {

            return $this->errorResponse([], $th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($id)
    {

        try {
            $role = $this->roleService->delete($id);

            return $this->successResponse($role, 'Role deleted successfully', Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {

            return $this->errorResponse([], $e->getMessage(), Response::HTTP_NOT_FOUND);
        } catch (\Throwable $th) {

            return $this->errorResponse([], $th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function assignRolePermission(AssignRolePermissionRequest $request)
    {

        try {
            $role = $this->roleService->assignRolePermission($request);

            return $this->successResponse($role, 'Permission assigned successfully', Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {

            return $this->errorResponse([], $e->getMessage(), Response::HTTP_NOT_FOUND);
        } catch (\Throwable $th) {

            return $this->errorResponse([], $th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

    public function removeRolePermission(RemoveRolePermissionRequest $request)
    {

        try {
            $role = $this->roleService->removeRolePermission($request);

            return $this->successResponse($role, 'Permission removed successfully', Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {

            return $this->errorResponse([], $e->getMessage(), Response::HTTP_NOT_FOUND);
        } catch (\Throwable $th) {

            return $this->errorResponse([], $th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

    public function assignUserRole(AssignUserRoleRequest $request)
    {

        try {
            $role = $this->roleService->assignUserRole($request);

            return $this->successResponse($role, 'Role assigned successfully', Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {

            return $this->errorResponse([], $e->getMessage(), Response::HTTP_NOT_FOUND);
        } catch (\Throwable $th) {

            return $this->errorResponse([], $th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

    public function removeUserRole(RemoveUserRoleRequest $request)
    {

        try {
            $role = $this->roleService->removeUserRole($request);

            return $this->successResponse($role, 'Role removed successfully', Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {

            return $this->errorResponse([], $e->getMessage(), Response::HTTP_NOT_FOUND);
        } catch (\Throwable $th) {

            return $this->errorResponse([], $th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }
}
