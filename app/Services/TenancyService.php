<?php

namespace App\Services;

use App\Models\Tenant;
use App\Models\User;
use Exception;

class TenancyService
{
    public function store($request)
    {
        $validatedData = $request->validated();

        // Create a new tenant
        $tenant = Tenant::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
        ]);

        // Add the domain
        $tenant->domains()->create([
            'domain' => $validatedData['domain'] . '.' . config('tenancy.central_domains')[1],
        ]);

        // Create the tenant admin user
        $tenant->run(function () use ($validatedData) {
            User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => bcrypt($validatedData['password']),
            ]);
        });

        // Return the created tenant
        return $tenant;
    }
}