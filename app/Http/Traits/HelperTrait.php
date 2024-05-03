<?php

namespace App\Http\Traits;

use Illuminate\Http\JsonResponse;
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
