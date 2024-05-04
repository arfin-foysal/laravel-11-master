<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePermissionRequest;
use App\Http\Requests\UpdatePermissionRequest;
use App\Http\Requests\UserPermissionAssignRequest;
use App\Http\Requests\UserPermissionRemoveRequest;
use App\Http\Traits\HelperTrait;
use App\Services\PermissionService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class PermissionController extends Controller
{
    use HelperTrait;

    protected $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    public function index(Request $request)
    {
        try {
            $permissions = $this->permissionService->index($request);

            return $this->successResponse($permissions, 'Permission list', Response::HTTP_OK);

        } catch (\Throwable $th) {
            return $this->errorResponse([], $th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id)
    {
        try {
            $permission = $this->permissionService->show($id);

            return $this->successResponse($permission, 'Permission detail', Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {

            return $this->errorResponse([], $e->getMessage(), Response::HTTP_NOT_FOUND);
        } catch (\Throwable $th) {

            return $this->errorResponse([], $th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(StorePermissionRequest $request)
    {
        try {
            $permission = $this->permissionService->create($request);

            return $this->successResponse($permission, 'Permission created', Response::HTTP_CREATED);
        } catch (ValidationException $e) {

            return $this->errorResponse($e->errors(), $e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Throwable $th) {

            return $this->errorResponse(
                $th->getMessage(), $th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(UpdatePermissionRequest $request, $id)
    {
        try {
            $permission = $this->permissionService->update($request, $id);

            return $this->successResponse($permission, 'Permission updated', Response::HTTP_OK);
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
            $permission = $this->permissionService->delete($id);

            return $this->successResponse($permission, 'Permission deleted', Response::HTTP_OK);

        } catch (ModelNotFoundException $e) {

            return $this->errorResponse([], $e->getMessage(), Response::HTTP_NOT_FOUND);

        } catch (\Throwable $th) {

            return $this->errorResponse([], $th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function userPermissionAssign(UserPermissionAssignRequest $request)
    {
        try {
            $permission = $this->permissionService->userPermissionAssign($request);

            return $this->successResponse($permission, 'Permission assigned', Response::HTTP_OK);

        } catch (ModelNotFoundException $e) {

            return $this->errorResponse([], $e->getMessage(), Response::HTTP_NOT_FOUND);

        } catch (\Throwable $th) {

            return $this->errorResponse([], $th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function userPermissionRemove(UserPermissionRemoveRequest $request)
    {
        try {
            $permission = $this->permissionService->userPermissionRemove($request);

            return $this->successResponse($permission, 'Permission removed', Response::HTTP_OK);

        } catch (ModelNotFoundException $e) {

            return $this->errorResponse([], $e->getMessage(), Response::HTTP_NOT_FOUND);

        } catch (\Throwable $th) {

            return $this->errorResponse([], $th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
