<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Traits\HelperTrait;
use App\Services\PermissionService;
use App\Http\Controllers\Controller;
use App\Http\Resources\PermissionResource;
use App\Http\Requests\Admin\PermissionRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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
            $pagination = $request->get('pagination', true);

            if ($pagination == true) {

                $permission = $this->permissionService->getAllWithPagination($request);

                return $this->successResponseWithPagination(
                    PermissionResource::collection($permission),
                    'Permission list',
                    Response::HTTP_OK
                );
            } else {

                $permission = $this->permissionService->getAll();
                return $this->successResponseWithPagination(
                    PermissionResource::collection($permission),
                    'Permission list',
                    Response::HTTP_OK
                );
            }
        } catch (\Throwable $th) {
            return $this->errorResponse([], $th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function edit($id)
    {
        try {
            $permission = $this->permissionService->edit($id);
            $permissionResource = new PermissionResource($permission);

            return $this->successResponse($permissionResource, 'Permission detail', Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {

            return $this->errorResponse([], $e->getMessage(), Response::HTTP_NOT_FOUND);
        } catch (\Throwable $th) {

            return $this->errorResponse([], $th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(PermissionRequest $request)
    {
        try {
            $permission = $this->permissionService->create($request);

            return $this->successResponse([], 'Permission created', Response::HTTP_CREATED);
        } catch (ValidationException $e) {

            return $this->errorResponse($e->errors(), $e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Throwable $th) {

            return $this->errorResponse([], $th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(PermissionRequest $request, $id)
    {
        try {
            $permission = $this->permissionService->update($request, $id);

            return $this->successResponse([], 'Permission updated', Response::HTTP_OK);
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

    public function userPermissionAssign (PermissionRequest $request)
    {
        try {
            $permission = $this->permissionService->userPermissionAssign($request);

            return $this->successResponse([], 'Permission assigned', Response::HTTP_OK);

        } catch (ModelNotFoundException $e) {

            return $this->errorResponse([], $e->getMessage(), Response::HTTP_NOT_FOUND);
            
        } catch (\Throwable $th) {

            return $this->errorResponse([], $th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
