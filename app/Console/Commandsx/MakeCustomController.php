<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class MakeCustomController extends Command
{
    protected $signature = 'make:custom-controller {name}';

    protected $description = 'Create a new custom controller class';

    protected $files;

    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    public function handle()
    {
        $name = $this->argument('name');
        $controllerName = "{$name}Controller";
        $resourceName = $name;  // Or you can use any transformation to get the resource name
        $path = $this->getPath($controllerName);

        if ($this->files->exists($path)) {
            $this->error('Controller already exists!');

            return false;
        }

        $this->makeDirectory($path);
        $this->files->put($path, $this->buildClass($controllerName, $resourceName));

        $this->info('Controller created successfully.');
    }

    protected function getPath($name)
    {
        return app_path("Http/Controllers/{$name}.php");
    }

    protected function makeDirectory($path)
    {
        if (! $this->files->isDirectory(dirname($path))) {
            $this->files->makeDirectory(dirname($path), 0755, true);
        }
    }

    protected function buildClass($name, $resourceName)
    {
        $stub = $this->files->get($this->getStub());

        return str_replace(
            ['DummyNamespace', 'DummyClass', 'DummyService', 'DummyStoreRequest', 'DummyUpdateRequest', 'DummyResource'],
            [$this->getNamespace(), $name, $resourceName.'Service', 'Store'.$resourceName.'Request', 'Update'.$resourceName.'Request', $resourceName],
            $stub
        );
    }

    protected function getStub()
    {
        return __DIR__.'/stubs/controller.stub';
    }

    protected function getNamespace()
    {
        return 'App\\Http\\Controllers';
    }
}
