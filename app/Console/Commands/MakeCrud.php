<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

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

        $this->info('CRUD operations created successfully.');
    }
}
