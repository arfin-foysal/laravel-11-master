<?php

namespace App\Http\Controllers\Tenancy;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTenancyRegister;
use App\Http\Traits\HelperTrait;
use App\Models\Tenant;
use App\Models\User;
use App\Services\TenancyService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TenancyRegisterController extends Controller
{
    use HelperTrait;

    private $service;

    public function __construct(TenancyService $service)
    {
        $this->service = $service;
    }

    public function store(StoreTenancyRegister $request)
    { 
            try {
                $resource = $this->service->store($request);

                return $this->successResponse($resource, 'Tenant created successfully!', Response::HTTP_CREATED);
            } catch (\Throwable $th) {
                return $this->errorResponse($th->getMessage(), 'Failed to create Tenant', Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        
    }
}
