<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class MakeCrud extends Command
{
    protected $signature = 'make:crud {name} {--fillable=}';

    protected $description = 'Create a new CRUD operations including controller, service, model, and request files';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $name = $this->argument('name');

        // Generate the model and request files
        $this->info('Creating model and request files...');
        Artisan::call('make:model-from-migration', ['name' => $name]);
        $this->info(Artisan::output());

        // Generate the service
        $this->info('Creating service...');
        Artisan::call('make:service', ['name' => $name.'Service']);
        $this->info(Artisan::output());

        // Generate the controller
        $this->info('Creating controller...');
        Artisan::call('make:custom-controller', ['name' => $name]);
        $this->info(Artisan::output());

        // Add the route and namespace to the routes file
        $this->addRouteAndNamespace($name);

        // Clear the route cache
        Artisan::call('route:clear');
        $this->info('Routes cache cleared.');

        $this->info('CRUD operations created successfully.');
    }

    protected function addRouteAndNamespace($name)
    {
        $routeFile = base_path('routes/api.php'); // Adjust this path if needed
        $controllerName = $name.'Controller';
        $namespaceLine = "use App\\Http\\Controllers\\$controllerName;";
        $routeSnippet = "    Route::apiResource('".strtolower($name)."s', $controllerName::class);";

        // Read the current routes file content
        $content = File::get($routeFile);

        // Find the position of the "use Illuminate\Support\Facades\Route;" line
        $position = strpos($content, 'use Illuminate\Support\Facades\Route;');

        if ($position !== false) {
            // Insert the namespace line before the "use Illuminate\Support\Facades\Route;" line
            $content = substr_replace($content, $namespaceLine."\n", $position, 0);
        }

        // Find the position where to add the new route, just after Route::middleware(['auth:sanctum'])->group(function () {
        $middlewarePosition = strpos($content, "Route::middleware(['auth:sanctum', 'role:admin,user'])->group(function () {");

        if ($middlewarePosition !== false) {
            // Find the next line after the group function, where the route should be added
            $insertionPoint = strpos($content, '{', $middlewarePosition) + 1;

            // Insert the new route below the Route::middleware(['auth:sanctum'])->group(function () { line
            $content = substr_replace($content, "\n".$routeSnippet, $insertionPoint, 0);
        }

        // Write the updated content back to the routes file
        File::put($routeFile, $content);

        $this->info('Route and namespace added successfully.');
    }
}
