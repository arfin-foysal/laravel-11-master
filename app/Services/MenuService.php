<?php

namespace App\Services;

use App\Models\Menu;
use App\Http\Traits\HelperTrait;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class MenuService
{
    use HelperTrait;

    public function index(Request $request): Collection|LengthAwarePaginator|array
    {
        $query = Menu::query();
        $query->with('subMenus');

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
            $data = $this->prepareMenuData($request);

            // Create the menu and associate roles
            $menu = Menu::create($data);

            // Sync the roles using the attach method
            if ($request->has('role_ids')) {
                $menu->roles()->attach($request->input('role_ids'));
            }

            // Commit the transaction if everything is successful
            DB::commit();

            return $menu;
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
            $menu = Menu::findOrFail($id);
            $updateData = $this->prepareMenuData($request, false);
            $menu->update($updateData);

            // Sync the roles using the sync method
            if ($request->has('role_ids')) {
                $menu->roles()->sync($request->input('role_ids'));
            }

            // Commit the transaction if everything is successful
            DB::commit();

            return $menu;
        } catch (\Exception $e) {
            // Rollback the transaction if any exception occurs
            DB::rollBack();

            // Optionally, you can rethrow the exception or return an error response
            throw $e;
        }
    }

    private function prepareMenuData(Request $request, bool $isNew = true): array
    {
        // Get the fillable fields from the model
        $fillable = (new Menu())->getFillable();

        // Extract relevant fields from the request dynamically
        $data = $request->only($fillable);

        // Handle file uploads
        // $data['icon'] = $this->ftpFileUpload($request, 'icon', 'image');
        // $data['cover_picture'] = $this->ftpFileUpload($request, 'cover_picture', 'menu');

        // Add created_by and created_at fields for new records
        if ($isNew) {
            $data['created_by'] = auth()->user()->id;
            $data['created_at'] = now();
        }

        return $data;
    }

    public function show(int $id): Menu
    {
        $menu = Menu::with([
            'subMenus' => function ($query) {
                $query->where('is_active', true) // Filter by active submenus
                      ->orderBy('order'); // Order submenus by 'order' column
            },
            'roles:id,name' // Only select 'id' and 'name' for roles
        ])->findOrFail($id);
    
        // Hide the pivot field from the roles relationship
        $menu->roles->makeHidden('pivot');
    
        return $menu;
    }
    


    public function destroy(int $id): bool
    {
        $menu = Menu::findOrFail($id);
        $menu->name .= '_' . Str::random(8);
        $menu->deleted_at = now();

        return $menu->save();
    }
}
