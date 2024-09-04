<?php

namespace App\Http\Traits;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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

    public function customResponse($data, $message, $status = true, $statusCode = 200): JsonResponse
    {
        $array = [
            'data' => $data,
            'message' => $message,
            'status' => $status,
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
    protected function fileUpload($request, $fileName, $destination)
    {
        if ($request->hasFile($fileName)) {
            $file = $request->file($fileName);
            $fileName = $fileName.'-'.Str::random(6).time().'.'.$file->getClientOriginalExtension();
            $destinationPath = 'uploads/'.trim($destination, '/');

            // Ensure the destination directory exists
            if (! is_dir($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            $file->move($destinationPath, $fileName);

            return $destinationPath.'/'.$fileName;
        }

        return null;
    }

    protected function sFileUpload($request, $fileName, $destination)
    {
        if ($request->hasFile($fileName)) {
            $file = $request->file($fileName);
            $fileName = $fileName.'-'.Str::random(6).time().'.'.$file->getClientOriginalExtension();
            $destinationPath = trim($destination, '/');

            // Store the file on the 'public' disk
            $path = $file->storeAs($destinationPath, $fileName, 'public');

            return Storage::url($path);
        }

        return null;
    }
    protected function ftpFileUpload($fullRequest, $fileName, $destination)
    {
        if ($fullRequest->hasFile($fileName)) {
            $file_temp = $fullRequest->file($fileName);
            $destinations = 'uploads/' . $destination;
    
            // Create directory if it doesn't exist and set permissions
            if (!Storage::exists($destinations)) {
                Storage::makeDirectory($destinations);
            }
    
            $file_url = Storage::put($destinations, $file_temp);
    
            return "crm/{$file_url}";
        }
    
        return null;
    }
    
    
    // Upload and Replace file

    /**
     * Create an Unauthorize JSON response.
     *
     * @param  $fullRequest  (provide full request Ex: $request)
     * @param  $fileName  (provide file name Ex: $request->image)
     * @param  $destination  (provide destination folder name Ex:'images')
     * @param  string  $oldFile  (provide old file name if you want to delete old file Ex: $userData->old_image)
     */

    /**
     * Create an Unauthorize JSON response.
     *
     * @param  $file  (provide file name Ex: $request->image)
     */
    protected function fileUploadAndUpdate($request, $fileName, $destination, $oldFile = null)
    {
        if ($request->hasFile($fileName)) {
            // Remove the old file if it exists
            if ($oldFile) {
                $oldFilePath = public_path('uploads/'.trim($oldFile, '/'));
                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath);
                }
            }

            // Handle the new file upload
            $file = $request->file($fileName);
            $newFileName = $fileName.'-'.Str::random(6).time().'.'.$file->getClientOriginalExtension();
            $destinationPath = 'uploads/'.trim($destination, '/');

            // Ensure the destination directory exists
            if (! is_dir($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            $file->move($destinationPath, $newFileName);

            return $destinationPath.'/'.$newFileName;
        }

        return $oldFile; // Return old file path if no new file is uploaded
    }

    protected function sFileUploadAndUpdate($request, $fileName, $destination, $oldFile = null)
    {
        if ($request->hasFile($fileName)) {
            // Remove the old file if it exists
            if ($oldFile) {
                $oldFilePath = str_replace('storage/', '', $oldFile); // Remove 'storage/' prefix
                if (Storage::disk('public')->exists($oldFilePath)) {
                    Storage::disk('public')->delete($oldFilePath);
                }
            }

            // Handle the new file upload
            $file = $request->file($fileName);
            $newFileName = $fileName.'-'.Str::random(6).time().'.'.$file->getClientOriginalExtension();
            $destinationPath = trim($destination, '/');

            // Store the file on the 'public' disk
            $path = $file->storeAs($destinationPath, $newFileName, 'public');

            return Storage::url($path);
        }

        return $oldFile; // Return old file path if no new file is uploaded
    }

    protected function ftpFileUploadAndUpdate($fullRequest, $fileName, $destination, $oldFile = null)
    {
        if ($fullRequest->hasFile($fileName)) {
            // Delete old file if it exists
            if ($oldFile) {
                $old_image_path = 'uploads/'.$oldFile;
                if (Storage::exists($old_image_path)) {
                    Storage::delete($old_image_path);
                }
            }

            // Upload new file
            $file_temp = $fullRequest->file($fileName);
            $destinations = 'uploads/'.$destination;
            $file_url = Storage::put($destinations, $file_temp);

            return "crm/{$file_url}";
        }

        return null;
    }

    // Delete File

    protected function deleteFile($file)
    {
        $filePath = public_path('uploads/'.trim($file, '/'));

        if (file_exists($filePath) && is_file($filePath)) {
            unlink($filePath);
        }

        return true;
    }

    protected function sDeleteFile($file)
    {
        // Convert the file URL to a relative path
        $filePath = str_replace('storage/', '', $file);

        // Check if the file exists on the 'public' disk and delete it
        if (Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);
        }

        return true;
    }

    protected function ftpDeleteFile($file)
    {
        $file_path = 'uploads/'.$file;

        if (Storage::exists($file_path)) {
            Storage::delete($file_path);
        }

        return true;
    }
}
