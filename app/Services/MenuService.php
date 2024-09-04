<?php

namespace App\Services;

use App\Models\Menu;
use App\Http\Traits\HelperTrait;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
        $data = $this->prepareMenuData($request);

        return Menu::create($data);
    }

    private function prepareMenuData(Request $request, bool $isNew = true): array
    {
        // Get the fillable fields from the model
        $fillable = (new Menu())->getFillable();

        // Extract relevant fields from the request dynamically
        $data = $request->only($fillable);

        // Handle file uploads
        // $data['icon'] = $this->ftpFileUpload($request, 'icon', 'image');
        //$data['cover_picture'] = $this->ftpFileUpload($request, 'cover_picture', 'menu');

        // Add created_by and created_at fields for new records
        if ($isNew) {
            $data['created_by'] = auth()->user()->id;
            $data['created_at'] = now();
        }

        return $data;
    }

    public function show(int $id): Menu
    {
        return Menu::with('subMenus')->findOrFail($id);
    }

    public function update(Request $request, int $id)
    {
        $menu = Menu::findOrFail($id);
        $updateData = $this->prepareMenuData($request, false);
        $menu->update($updateData);

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
