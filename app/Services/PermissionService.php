<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Pagination\Paginator;
use Spatie\Permission\Models\Permission;

class PermissionService
{
    public function __construct()
    {
    }

    public function index($request)
    {

        try {
            $permission = Permission::select('*');
            // Select specific columns
            $columns = ['id', 'name']; // Define the columns you want to select
            $permission->select($columns);

            // Sorting
            $sortBy = $request->input('sortBy', 'id');
            $sortDesc = $request->boolean('sortDesc') ? 'desc' : 'asc';
            $permission->orderBy($sortBy, $sortDesc);

            // Searching
            $searchValue = $request->input('search');
            if ($searchValue) {
                $permission->where(function ($query) use ($searchValue) {
                    $query->whereRaw('LOWER(id) LIKE ?', ['%'.strtolower($searchValue).'%'])
                        ->orWhereRaw('LOWER(name) LIKE ?', ['%'.strtolower($searchValue).'%']);
                });
            }

            // Check for pagination
            $pagination = $request->boolean('pagination', true); // Default to true if not specified

            if ($pagination) {
                // Paginated data
                $itemsPerPage = $request->input('itemsPerPage', 10);

                // Manually paginate the results
                $currentPage = Paginator::resolveCurrentPage('page');
                $courseTypeResults = $permission->paginate($itemsPerPage, $columns, 'page', $currentPage);

                return $courseTypeResults;
            }

            // All data (no pagination)
            return $permission->get();
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function show($id)
    {
        try {
            $permission = Permission::findOrFail($id);

            return $permission;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function create($request)
    {
        try {

            // $permission = new Permission();
            // $permission->name = $request->name;
            // $permission->save();

            //bulk insert
            $permissions = [];
            foreach ($request->names as $name) {
                $permissions[] = ['name' => $name, 'guard_name' => 'api', 'created_at' => now()];
            }
            Permission::insert($permissions);

            return $permissions;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function update($request, $id)
    {
        try {
            $permission = Permission::findOrFail($id);
            $permission->name = $request->name;
            $permission->save();

            return $permission;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function delete($id)
    {
        try {
            $permission = Permission::where('id', $id)->first();
            // Check if assigned to any role and any user
            if ($permission->roles()->count() > 0 || $permission->users()->count() > 0) {
                throw new \Exception('Permission is assigned to a role or user.');
            }
            if (! $permission) {
                throw new \Exception('Record not found.');
            }
            $permission->delete();

            return $permission;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function userPermissionAssign($request)
    {
        try {
            $user = User::findOrFail($request->user_id);
            $user->syncPermissions($request->permissions);

            return $user;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function userPermissionRemove($request)
    {
        try {
            $user = User::findOrFail($request->user_id);
            $permission = Permission::where('id', $request->permission_id)
                ->where('guard_name', 'api')
                ->firstOrFail();
            $user->revokePermissionTo($permission->name);

            return $user;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
