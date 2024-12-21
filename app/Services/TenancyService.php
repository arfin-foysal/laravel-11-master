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
            'password' => $validatedData['password'],
            'company' => $validatedData['company'],
            'username' => $validatedData['username'],
            'number' => $validatedData['number'],
        ]);

        // Add the domain
        $tenant->domains()->create([
            'domain' => $validatedData['domain'] . '.' . config('tenancy.central_domains')[1],
        ]);


        // Create the tenant admin user
        $tenant->run(function () use ($validatedData) {
            User::create([
                'name' => $validatedData['name'],
                'username' => $validatedData['username'],
                'number' => $validatedData['number'],
                'email' => $validatedData['email'],
                'password' => bcrypt($validatedData['password']),
            ]);
        });

        //and save tenancy_db_name
        $tenant->update([
            'database' => 'tenant' . $tenant->id,
        ]);

        // Return the created tenant
        return $tenant;
    }
}