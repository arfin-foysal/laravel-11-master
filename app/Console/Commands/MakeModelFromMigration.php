<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class MakeModelFromMigration extends Command
{
    protected $signature = 'make:model-from-migration {name}';

    protected $description = 'Create a new model with fillable attributes and request files based on an existing migration';

    protected $fileSystem;

    public function __construct(Filesystem $fileSystem)
    {
        parent::__construct();
        $this->fileSystem = $fileSystem;
    }

    public function handle()
    {
        $name = $this->argument('name');
        $tableName = Str::plural(Str::snake($name));

        $migrationFile = $this->getMigrationFile($tableName);

        if ($migrationFile) {
            $columns = $this->extractColumnsFromMigration($migrationFile);
            $fillable = implode(',', array_column($columns, 'name'));

            $this->info("Creating model with fillable attributes: $fillable...");
            Artisan::call('make:model', ['name' => $name]);
            $this->addFillableAttributes($name, $fillable);

            $this->info('Creating request files...');
            $this->createRequestFiles($name, $columns);

            $this->info('Model and request files created successfully.');
        } else {
            $this->error("Migration file for table '$tableName' not found.");
        }
    }

    protected function getMigrationFile($tableName)
    {
        $migrationsPath = database_path('migrations');
        $files = $this->fileSystem->files($migrationsPath);

        foreach ($files as $file) {
            if (Str::contains($file->getFilename(), "create_{$tableName}_table")) {
                return $file->getPathname();
            }
        }

        return null;
    }

    protected function extractColumnsFromMigration($migrationFile)
    {
        $content = $this->fileSystem->get($migrationFile);
        preg_match_all('/\$table->(.*?)\(\'(.*?)\'\)/', $content, $matches);

        $columns = [];
        foreach ($matches[2] as $key => $column) {
            if (! in_array($column, ['id', 'created_at', 'updated_at', 'deleted_at']) && ! $this->isForeignKey($matches[1][$key])) {
                $columns[] = ['name' => $column, 'type' => $matches[1][$key]];
            }
        }

        return $columns;
    }

    protected function isForeignKey($definition)
    {
        return Str::contains($definition, 'foreign');
    }

    protected function addFillableAttributes($name, $fillable)
    {
        $path = app_path("Models/{$name}.php");

        if ($this->fileSystem->exists($path)) {
            $content = $this->fileSystem->get($path);

            // Add fillable attributes
            $fillableArray = explode(',', $fillable);
            $fillableString = implode("', '", array_map('trim', $fillableArray));
            $fillableString = "['".$fillableString."']";

            $fillableTemplate = "
    
        protected \$fillable = $fillableString;
            ";

            // Add use SoftDeletes; after use HasFactory;
            if (Str::contains($content, 'use HasFactory;')) {
                $content = str_replace('use HasFactory;', "use HasFactory;\nuse SoftDeletes;\n$fillableTemplate", $content);
            } else {
                $content = str_replace('use Illuminate\\Database\\Eloquent\\Model;', "use Illuminate\\Database\\Eloquent\\Model;\nuse SoftDeletes;", $content);
            }

            $this->fileSystem->put($path, $content);
        } else {
            $this->error("Model file does not exist: $path");
        }
    }

    protected function createRequestFiles($name, $columns)
    {
        $storeRequest = "Store{$name}Request";
        $updateRequest = "Update{$name}Request";

        Artisan::call('make:request', ['name' => $storeRequest]);
        Artisan::call('make:request', ['name' => $updateRequest]);

        $this->addRulesToRequest($storeRequest, $columns);
        $this->addRulesToRequest($updateRequest, $columns);
    }

    protected function addRulesToRequest($requestName, $columns)
    {
        $path = app_path("Http/Requests/{$requestName}.php");

        if ($this->fileSystem->exists($path)) {
            $content = $this->fileSystem->get($path);

            $rulesArray = [];
            foreach ($columns as $column) {
                $rulesArray[] = "'{$column['name']}' => 'required'";
            }
            $rulesString = implode(",\n            ", $rulesArray);

            $authorizeTemplate = '
    public function authorize(): bool
    {
        return true;
    }
            ';

            $rulesTemplate = "
    public function rules(): array
    {
        return [
            $rulesString
        ];
    }
            ";

            // Set authorize to true and add rules
            $content = preg_replace(
                [
                    '/public function authorize\(\): bool\s*{\s*return false;\s*}/',
                    '/public function rules\(\): array\s*{\s*return \[\];\s*}/',
                ],
                [
                    $authorizeTemplate,
                    $rulesTemplate,
                ],
                $content
            );

            $this->fileSystem->put($path, $content);
        } else {
            $this->error("Request file does not exist: $path");
        }
    }
}
