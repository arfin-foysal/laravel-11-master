<?php

namespace App\Http\Traits;

use Illuminate\Support\Str;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;

trait HelperTrait
{
    
    protected function successResponse($data, $message, $statusCode = 200): JsonResponse
    {
        $array = [
            'data' => $data,
            'message' => $message,
        ];

        return response()->json($array, $statusCode);
    }

    protected function errorResponse($error, $message, $statusCode): JsonResponse
    {
        $array = [
            'errors' => $error,
            'message' => $message,
        ];

        return response()->json($array, $statusCode);
    }

    /**
     * Create an Unauthorize JSON response.
     */
    protected function noAuthResponse(): JsonResponse
    {
        return response()->json([
            'data' => [],
            'message' => 'Unauthorized.',
            'status' => false,
        ], 401);
    }

    protected function codeGenerator($prefix, $model)
    {
        if ($model::count() == 0) {
            $newId = $prefix.'-'.str_pad(1, 5, 0, STR_PAD_LEFT);

            return $newId;
        }
        $lastId = $model::orderBy('id', 'desc')->first()->id;
        $lastIncrement = substr($lastId, -3);
        $newId = $prefix.'-'.str_pad($lastIncrement + 1, 5, 0, STR_PAD_LEFT);
        $newId++;

        return $newId;
    }


    private function applySorting($query, Request $request): void
    {
        $sortBy = $request->input('sortBy', 'id');
        $sortDesc = $request->boolean('sortDesc', true) ? 'desc' : 'asc';
        $query->orderBy($sortBy, $sortDesc);
    }

    private function applySearch($query, ?string $searchValue, array $searchKeys): void
    {
        if ($searchValue) {
            $query->where(function ($query) use ($searchValue, $searchKeys) {
                foreach ($searchKeys as $key) {
                    $query->orWhereRaw('LOWER('.$key.') LIKE ?', ['%'.strtolower($searchValue).'%']);
                }
            });
        }
    }


    private function paginateOrGet($query, Request $request): Collection|LengthAwarePaginator|array
    {
        if ($request->boolean('pagination', true)) {
            $itemsPerPage = $request->input('itemsPerPage', 10);
            $currentPage = Paginator::resolveCurrentPage('page');

            return $query->paginate($itemsPerPage, ['*'], 'page', $currentPage);
        }

        return $query->get();
    }

    private function applyFilters($query, $request, $filters)
    {
        foreach ($filters as $key => $operator) {
            if ($request->filled($key)) {
                $value = $request->input($key);
                switch ($operator) {
                    case '=':
                        $query->where($key, '=', $value);
                        break;
                    case 'like':
                        $query->where($key, 'like', '%'.$value.'%');
                        break;
                    case '>':
                    case '<':
                    case '>=':
                    case '<=':
                        $query->where($key, $operator, $value);
                        break;
                        // Add more cases for additional operators as needed.
                    default:
                        // Handle other operators or throw an exception if needed.
                        break;
                }
            }
        }
    }

    /**
     * Create an Unauthorize JSON response.
     *
     * @param  $fullRequest  (provide full request Ex: $request)
     * @param  $fileName  (provide file name Ex: $request->image)
     * @param  $destination  (provide destination folder name Ex:'images')
     */
    protected function fileUpload($fullRequest, $fileName, $destination)
    {
        $file = null;
        $file_url = null;
        if ($fullRequest->hasFile($fileName)) {
            $image = $fullRequest->file($fileName);
            $time = time();
            $file = $fileName.'-'.Str::random(6).$time.'.'.$image->getClientOriginalExtension();
            $destinations = 'uploads/'.$destination;
            $image->move($destinations, $file);
            $file_url = $destination.'/'.$file;
        }

        return $file_url;
    }

    /**
     * Create an Unauthorize JSON response.
     *
     * @param  $fullRequest  (provide full request Ex: $request)
     * @param  $fileName  (provide file name Ex: $request->image)
     * @param  $destination  (provide destination folder name Ex:'images')
     * @param  string  $oldFile  (provide old file name if you want to delete old file Ex: $userData->old_image)
     */
    protected function fileUploadAndUpdate($fullRequest, $fileName, $destination, $oldFile = null)
    {
        $file = null;
        $file_url = null;
        if ($fullRequest->hasFile($fileName)) {
            if ($oldFile) {
                $old_image_path = public_path('uploads/'.$oldFile);
                if (file_exists($old_image_path)) {
                    unlink($old_image_path);
                }
            }
            $image = $fullRequest->file($fileName);
            $time = time();
            $file = $fileName.'-'.Str::random(6).$time.'.'.$image->getClientOriginalExtension();
            $destinations = 'uploads/'.$destination;
            $image->move($destinations, $file);
            $file_url = $destination.'/'.$file;
        }

        return $file_url;
    }

    /**
     * Create an Unauthorize JSON response.
     *
     * @param  $file  (provide file name Ex: $request->image)
     */
    protected function deleteFile($file)
    {
        $image_path = public_path('uploads/'.$file);
        if (file_exists($image_path)) {
            unlink($image_path);
        }

        return true;
    }
}
