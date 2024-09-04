<?php

namespace App\Services;

use App\Models\SubMenu;
use App\Http\Traits\HelperTrait;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
        $data = $this->prepareSubMenuData($request);

        return SubMenu::create($data);
    }

    private function prepareSubMenuData(Request $request, bool $isNew = true): array
    {
        // Get the fillable fields from the model
        $fillable = (new SubMenu())->getFillable();

        // Extract relevant fields from the request dynamically
        $data = $request->only($fillable);

        // Handle file uploads
        // $data['icon'] = $this->ftpFileUpload($request, 'icon', 'image');
        //$data['cover_picture'] = $this->ftpFileUpload($request, 'cover_picture', 'subMenu');

        // Add created_by and created_at fields for new records
        if ($isNew) {
            $data['created_by'] = auth()->user()->id;
            $data['created_at'] = now();
        }

        return $data;
    }

    public function show(int $id): SubMenu
    {
        return SubMenu::findOrFail($id);
    }

    public function update(Request $request, int $id)
    {
        $subMenu = SubMenu::findOrFail($id);
        $updateData = $this->prepareSubMenuData($request, false);
        $subMenu->update($updateData);

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
