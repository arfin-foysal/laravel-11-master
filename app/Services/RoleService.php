<?php

namespace App\Services;

use App\Models\CustomRole;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;

class RoleService
{
    public function __construct()
    {
        //
    }

    public function index($request)
    {

        try {
            $role = CustomRole::select('*')->with('permissions');
            // Select specific columns
            $columns = ['id', 'name']; // Define the columns you want to select
            $role->select($columns);

            // Sorting
            $sortBy = $request->input('sortBy', 'id');
            $sortDesc = $request->boolean('sortDesc') ? 'desc' : 'asc';
            $role->orderBy($sortBy, $sortDesc);

            // Searching
            $searchValue = $request->input('search');
            if ($searchValue) {
                $role->where(function ($query) use ($searchValue) {
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
                $courseTypeResults = $role->paginate($itemsPerPage, $columns, 'page', $currentPage);

                return $courseTypeResults;
            }

            // All data (no pagination)
            return $role->get();
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function create(Request $request)
    {
        DB::beginTransaction();
        try {

            $role = new CustomRole();
            $role->name = $request->name;
            $role->save();

            $role->syncPermissions($request->permissions);

            DB::commit();

            return $role;
        } catch (\Throwable $th) {
            DB::rollback();

            throw $th;
        }
    }

    public function show($id)
    {
        try {

            $role = CustomRole::with('permissions')->find($id);

            return $role;
        } catch (\Throwable $th) {

            throw $th;
        }
    }

    public function update(Request $request, $id)
    {

        DB::beginTransaction();

        try {

            $role = CustomRole::find($id);
            $role->name = $request->name;
            $role->save();

            $role->syncPermissions($request->permissions);

            DB::commit();

            return $role;
        } catch (\Throwable $th) {
            DB::rollback();

            throw $th;
        }
    }

    public function delete($id)
    {
        DB::beginTransaction();
        try {
            $userCount = User::with('roles')
                ->get()->filter(
                    fn ($user) => $user->roles->where('id', $id)->toArray()
                )->count();

            if ($userCount > 0) {
                throw new \Exception('This role is assigned to some users. Please remove the role from users first.');
            }

            $role = CustomRole::find($id);
            if (! $role) {
                throw new \Exception('Record not found.');
            }

            $role->permissions()->detach();
            $response = $role->delete();

            DB::commit();

            return $response;
        } catch (\Exception $th) {
            DB::rollback();
            throw $th;
        }
    }

    public function assignRolePermission(Request $request)
    {

        DB::beginTransaction();
        try {
            $role = CustomRole::find($request->role_id);
            if (! $role) {
                throw new \Exception('Role not found.');
            }
            $role->syncPermissions($request->permissions);

            DB::commit();

            return $role;
        } catch (\Exception $th) {
            DB::rollback();
            throw $th;
        }
    }

    public function removeRolePermission(Request $request)
    {

        DB::beginTransaction();
        try {
            $role = CustomRole::find($request->role_id);
            if (! $role) {
                throw new \Exception('Role not found.');
            }
            if (! $role->hasPermissionTo($request->permission_id)) {
                throw new \Exception('Permission already removed.');
            }
            $role->revokePermissionTo($request->permission_id);

            DB::commit();

            return $role;
        } catch (\Exception $th) {
            DB::rollback();
            throw $th;
        }
    }

    public function assignUserRole(Request $request)
    {

        DB::beginTransaction();
        try {
            $user = User::find($request->user_id);
            if (! $user) {
                throw new \Exception('User not found.');
            }
            $role = CustomRole::find($request->role_id);

            $user->syncRoles($role->name);

            DB::commit();

            return $user;
        } catch (\Exception $th) {
            DB::rollback();
            throw $th;
        }
    }

    public function removeUserRole(Request $request)
    {

        DB::beginTransaction();
        try {
            $user = User::find($request->user_id);
            if (! $user) {
                throw new \Exception('User not found.');
            }
            $role = CustomRole::find($request->role_id);

            $user->removeRole($role->name);

            DB::commit();

            return $user;
        } catch (\Exception $th) {
            DB::rollback();
            throw $th;
        }
    }
}
