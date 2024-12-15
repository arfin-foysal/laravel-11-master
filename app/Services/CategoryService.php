<?php

namespace App\Services;

use App\Models\Category;
use App\Http\Traits\HelperTrait;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryService
{
    use HelperTrait;

    public function index(Request $request): Collection|LengthAwarePaginator|array
    {
        $query = Category::query();

        $query->when($request->has('is_active'), function ($q) use ($request) {
            $q->where('is_active', $request->input('is_active'));
        }, function ($q) {
            $q->where('is_active', true);
        });

        $query->with(['children' => function ($q) {
            $q->with('children');
        }]);

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
        $data = $this->prepareCategoryData($request);

        return Category::create($data);
    }

    private function prepareCategoryData(Request $request, bool $isNew = true): array
    {
        // Get the fillable fields from the model
        $fillable = (new Category())->getFillable();

        // Extract relevant fields from the request dynamically
        $data = $request->only($fillable);

        // Handle file uploads
        $data['image'] = $this->ftpFileUpload($request, 'image', 'image');
        //$data['cover_picture'] = $this->ftpFileUpload($request, 'cover_picture', 'category');

        // Add created_by and created_at fields for new records
        if ($isNew) {
            $data['created_by'] = auth()->user()->id;
            $data['created_at'] = now();
        }

        return $data;
    }

    public function show(int $id): Category
    {
        return Category::with(['children' => ['children']])->findOrFail($id);
    }



    public function update(Request $request, int $id)
    {
        $category = Category::findOrFail($id);
        $updateData = $this->prepareCategoryData($request, false);
        $category->update($updateData);

        return $category;
    }

    public function destroy(int $id): bool
    {
        $category = Category::findOrFail($id);
        $category->name .= '_' . Str::random(8);
        $category->deleted_at = now();

        return $category->save();
    }
}
