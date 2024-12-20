<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TenancyRegisterController;
use Illuminate\Support\Facades\Route;

// Dynamically retrieve the central domain
$centralDomains = config('tenancy.central_domains');
foreach ($centralDomains as $key => $domain) {
    Route::domain($domain)->group(function () {
        Route::get('/', function () {
            return view('welcome');
        });
    });
}
