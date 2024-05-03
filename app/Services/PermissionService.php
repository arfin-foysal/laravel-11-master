<?php

namespace App\Services;

use App\Models\AdminUser;
use App\Models\User;
use Spatie\Permission\Models\Permission;

class PermissionService
{

    public function __construct()
    {
    }

    public function getAllWithPagination($request)
    {
        $permission = Permission::select('*');

        if ($request->has('sortBy') && $request->has('sortDesc')) {
            $sortBy = $request->query('sortBy');

            $sortDesc = $request->query('sortDesc') == true ? 'desc' : 'asc';

            if ($sortBy === 'name') {
                $permission = $permission->orderBy('name', $sortDesc);
            } else {
                $permission = $permission->orderBy($sortBy, $sortDesc);
            }
        } else {
            $permission = $permission->orderBy('id', 'desc');
        }

        $searchValue = $request->input('search');
        $itemsPerPage = 10;

        if ($searchValue) {

            $permission->where(function ($query) use ($searchValue) {
                $query->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($searchValue) . '%']);
                 
            });

            if ($request->has('itemsPerPage')) {
                $itemsPerPage = $request->get('itemsPerPage');

                return $permission->paginate($itemsPerPage, ['*'], $request->get('page'));
            }
        } else {
            $itemsPerPage = 10;

            if ($request->has('itemsPerPage')) {
                $itemsPerPage = $request->get('itemsPerPage');

                return $permission->paginate($itemsPerPage);
            }
        }

        return $permission->paginate($itemsPerPage);
    }

 

    public function getAll()
    {
        try {
            $permissions = Permission::latest()->get();
            return $permissions;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function edit($id)
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
            
            $permission = new Permission();
            $permission->name = $request->name;
            $permission->save();

            return $permission;

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
            $permission = Permission::where('id', $id)->delete();
            if (!$permission) {
                throw new \Exception('Record not found.');
            }
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

    

}
