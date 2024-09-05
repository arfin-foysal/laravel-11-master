<?php

namespace App\Services;

use App\Models\SubMenu;
use App\Http\Traits\HelperTrait;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

use Illuminate\Support\Facades\DB;

class SubMenuService
{
    use HelperTrait;

    public function index(Request $request): Collection|LengthAwarePaginator|array
    {
        $query = SubMenu::query();

        // Select specific columns
        $query->select(['*']);

        // Sorting
        $this->applySorting($query, $request);

        // Searching
        $searchKeys = ['name']; // Define the fields you want to search by
        $this->applySearch($query, $request->input('search'), $searchKeys);

        // Pagination
        return $this->paginateOrGet($query, $request);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $data = $this->prepareSubMenuData($request);
            $subMenu = SubMenu::create($data);

            // Sync the roles using the attach method
            if ($request->has('role_ids')) {
                $subMenu->roles()->attach($request->input('role_ids'));
            }

            // Commit the transaction if everything is successful
            DB::commit();

            return $subMenu;
        } catch (\Exception $e) {
            // Rollback the transaction if any exception occurs
            DB::rollBack();

            // Optionally, you can rethrow the exception or return an error response
            throw $e;
        }
    }

    public function update(Request $request, int $id)
    {
        DB::beginTransaction();

        try {
            $subMenu = SubMenu::findOrFail($id);
            $updateData = $this->prepareSubMenuData($request, false);
            $subMenu->update($updateData);

            // Sync the roles using the sync method
            if ($request->has('role_ids')) {
                $subMenu->roles()->sync($request->input('role_ids'));
            }

            // Commit the transaction if everything is successful
            DB::commit();

            return $subMenu;
        } catch (\Exception $e) {
            // Rollback the transaction if any exception occurs
            DB::rollBack();

            // Optionally, you can rethrow the exception or return an error response
            throw $e;
        }
    }

    private function prepareSubMenuData(Request $request, bool $isNew = true): array
    {
        // Get the fillable fields from the model
        $fillable = (new SubMenu())->getFillable();

        // Extract relevant fields from the request dynamically
        $data = $request->only($fillable);

        // Handle file uploads
        // $data['icon'] = $this->ftpFileUpload($request, 'icon', 'image');
        // $data['cover_picture'] = $this->ftpFileUpload($request, 'cover_picture', 'subMenu');

        // Add created_by and created_at fields for new records
        if ($isNew) {
            $data['created_by'] = auth()->user()->id;
            $data['created_at'] = now();
        }

        return $data;
    }

    public function show(int $id): SubMenu
    {
        $subMenu = SubMenu::with([
            'roles:id,name' // Only select 'id' and 'name' for roles
        ])->findOrFail($id);
    
        // Hide the pivot field from the roles relationship
        $subMenu->roles->makeHidden('pivot');
    
        return $subMenu;
    }
    

    public function destroy(int $id): bool
    {
        $subMenu = SubMenu::findOrFail($id);
        $subMenu->name .= '_' . Str::random(8);
        $subMenu->deleted_at = now();

        return $subMenu->save();
    }
}

