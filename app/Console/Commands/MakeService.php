<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class MakeService extends Command
{
    protected $signature = 'make:service {name}';

    protected $description = 'Create a new service class';

    protected $files;

    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    public function handle()
    {
        $name = $this->argument('name');
        $modelName = str_replace('Service', '', $name); // Derive model name from service name
        $variableName = lcfirst($modelName); // Create variable name from model name
        $path = $this->getPath($name);

        if ($this->files->exists($path)) {
            $this->error('Service already exists!');

            return false;
        }

        $this->makeDirectory($path);
        $this->files->put($path, $this->buildClass($name, $modelName, $variableName));

        $this->info('Service created successfully.');
    }

    protected function getPath($name)
    {
        return app_path("Services/{$name}.php");
    }

    protected function makeDirectory($path)
    {
        if (! $this->files->isDirectory(dirname($path))) {
            $this->files->makeDirectory(dirname($path), 0755, true);
        }
    }

    protected function buildClass($name, $modelName, $variableName)
    {
        $stub = $this->files->get($this->getStub());

        return str_replace(
            ['DummyNamespace', 'DummyClass', 'DummyModel', 'dummyModel'],
            [$this->getNamespace(), $name, $modelName, $variableName],
            $stub
        );
    }

    protected function getStub()
    {
        return __DIR__.'/stubs/service.stub';
    }

    protected function getNamespace()
    {
        return 'App\\Services';
    }
}
