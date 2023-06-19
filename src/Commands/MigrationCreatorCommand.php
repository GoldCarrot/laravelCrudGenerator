<?php

namespace Chatway\LaravelCrudGenerator\Commands;

use Chatway\LaravelCrudGenerator\Core\Helpers\ConsoleHelper;
use Chatway\LaravelCrudGenerator\Core\Helpers\EnvHelper;
use Chatway\LaravelCrudGenerator\CoreMigration\DTO\ColumnMigrationDTO;
use Illuminate\Console\Command;

class MigrationCreatorCommand extends Command
{
    protected $signature = '
    migrate:new
    {name : migration name}
    {--scenario= : Scenario, create your custom class for generate custom list files"}
    {--fields= : Fields list example --fields=title:string,slug:string:notNull"}
    ';

    public function handle(): int
    {
        $migrationName = $this->argument('name');
        $scenario = $this->option('scenario');
        $fields = $this->option('fields');
        $this->makeMigrationByLaravel($migrationName);
        $migrationFilename = $this->getLastMigrationFilename();
        if (EnvHelper::devMode() && str_contains($migrationFilename, '_1.php')) {
            $migrationFilename = $this->getLastMigrationFilename(2);
        }
        if ($migrationFilename) {
            $tableName = $this->getTableName($migrationName);
            if (strlen($scenario) === 0) {
                $columns = $this->prepareColumnList($migrationName, $fields);
                $this->info('Column list prepared. Count columns = ' . count($columns));
                $columnsForCreate = '';
                $columnsForDrop = '';
                foreach ($columns as $index => $column) {
                    $type = $column->foreignId ? 'foreignId' : ($column->foreignUuid ? 'foreignUuid' : $column->type);
                    if ($column->templateForCreate) {
                        $columnsForCreate .= ($index ? '            ' : '') . "\$table->$column->templateForCreate;\n";
                    } else {
                        $columnsForCreate .= ($index ? '            ' : '') . "\$table->$type('$column->name')";
                        $columnsForCreate .= ($column->nullable ? '->nullable()' : '');
                        $columnsForCreate .= ($column->default ? "->default($column->default)" : '');
                        $columnsForCreate .= ($column->foreignId || $column->foreignUuid ? '->constrained()' : '');
                        $columnsForCreate .= ";\n";
                    }
                    if ($column->templateForDrop) {
                        $columnsForDrop .= ($index ? '            ' : '') . "\$table->$column->templateForDrop;\n";
                    } else {
                        $columnsForDrop .= ($index ? '            ' : '') . "\$table->dropColumn('$column->name');\n";
                    }
                }
                $fileClass = file_get_contents($this->getMigrationsPath() . DIRECTORY_SEPARATOR . $migrationFilename);
                $fileClass = str_replace($migrationName, $tableName, $fileClass);
                if (str_contains($migrationName, 'add_')) {
                    $fileClass = str_replace('Schema::create', 'Schema::table', $fileClass);
                    $fileClass = str_replace("Schema::dropIfExists('$tableName');", "Schema::table('$tableName', function (Blueprint \$table) {
            //
        });", $fileClass);
                }

                if (str_contains($migrationName, 'add_')) {
                    $fileClass = preg_replace('/\/\/\n/', $columnsForCreate, $fileClass, 1);
                    $fileClass = preg_replace('/\/\/\n/', $columnsForDrop, $fileClass, 1);
                } else {
                    if (!str_contains($fileClass, $columnsForCreate)) {
                        $addStringToPos = strpos($fileClass, '$table->timestamps()');
                        $fileClass = substr_replace($fileClass, $columnsForCreate . '            ', $addStringToPos, 0);
                    } else {
                        $this->warn('Migrate is updated');
                    }
                }
            } else {
                $path = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'CoreMigration' . DIRECTORY_SEPARATOR . 'Templates';
                if (file_exists(storage_path('app') . DIRECTORY_SEPARATOR . "$scenario.json")) {
                    $template = storage_path('app') . DIRECTORY_SEPARATOR . "$scenario.json";
                } elseif (file_exists($path . DIRECTORY_SEPARATOR . "$scenario.json")) {
                    $template = $path . DIRECTORY_SEPARATOR . "$scenario.json";
                } else {
                    $this->error('File not exists: ' . $path . DIRECTORY_SEPARATOR . "$scenario.json");
                    die;
                }
                ConsoleHelper::info("File with templates: $template");
                $template = json_decode(file_get_contents($template), true);
                $fileClass = file_get_contents($this->getMigrationsPath() . DIRECTORY_SEPARATOR . $migrationFilename);
                $fileClass = str_replace($migrationName, $tableName, $fileClass);

                if (!str_contains($fileClass, $template['up'])) {
                    $fileClass = str_replace("\$table->timestamps();\n        });\n", $template['up'], $fileClass);
                    if (array_key_exists('down', $template) && $template['down']) {
                        $fileClass = str_replace("Schema::dropIfExists('$tableName');", $template['down'], $fileClass);
                    }
                } else {
                    $this->warn('Migrate is updated');
                }
            }
            $tempFile = $this->getMigrationsPath() . DIRECTORY_SEPARATOR . $migrationFilename;
            if (EnvHelper::devMode()) {
                $tempFile = str_contains($tempFile, '_1.php') ? $tempFile : str_replace(['.php'], '_1.php', $tempFile);
            }
            file_put_contents($tempFile, $fileClass);
        }
        return 0;
    }

    /**
     * @param $migrationName
     * @param $fields
     *
     * @return array|ColumnMigrationDTO[]
     */
    private function prepareColumnList($migrationName, $fields): array
    {
        $tableName = $this->getTableName($migrationName);
        $fieldsFromMigrationName = preg_replace("/^\d+_|add_|_to_{$tableName}_table/", '', $migrationName);
        $fieldsSimpleArray = str_contains($fieldsFromMigrationName, 'add_') ? explode('_and_', $fieldsFromMigrationName) : [$fieldsFromMigrationName];
        $fields = $fields ? explode(',', $fields) : [];
        $columns = [];
        if (file_exists(storage_path('app') . DIRECTORY_SEPARATOR . "fieldTemplates.json")) {
            $pathToPackage = storage_path('app') . DIRECTORY_SEPARATOR . "fieldTemplates.json";
        } else {
            $pathToPackage = dirname(__DIR__) . '/CoreMigration/fieldTemplates.json';
        }
        ConsoleHelper::info("File with templates: $pathToPackage");
        $fieldTemplates = json_decode(file_get_contents($pathToPackage), true);
        $templateFieldNames = array_column($fieldTemplates, 'name');
        foreach ($fieldsSimpleArray as $item) {
            $index = array_search($item, $templateFieldNames);
            if ($index !== false) {
                $fieldTemplate = $fieldTemplates[$index];
                $columns[] = new ColumnMigrationDTO(
                    $item,
                    $fieldTemplate['type'] ?? null,
                    $fieldTemplate['nullable'] ?? true,
                    $fieldTemplate['foreignUuid'] ?? false,
                    $fieldTemplate['foreignId'] ?? false,
                    $fieldTemplate['template'] ?? null,
                );
            } else {
                $columns[] = new ColumnMigrationDTO($item);
            }
        }
        foreach ($fields as $field) {
            $fieldWithParamsArray = explode(':', $field);
            if (count($fieldWithParamsArray)) {
                $index = array_search($fieldWithParamsArray[0], array_column($columns, 'name'));
                if ($index !== false) {
                    unset($columns[$index]);
                    if (count($columns)) {
                        $columns = array_values($columns);
                    }
                }
                if (count($fieldWithParamsArray) == 1
                    && ($indexField = array_search($fieldWithParamsArray[0], $templateFieldNames)) !== false
                ) {
                    $fieldTemplate = $fieldTemplates[$indexField];
                    $columns[] = new ColumnMigrationDTO(
                        $fieldWithParamsArray[0],
                        $fieldTemplate['type'] ?? null,
                        $fieldTemplate['nullable'] ?? true,
                        $fieldTemplate['foreignUuid'] ?? false,
                        $fieldTemplate['foreignId'] ?? false,
                        $fieldTemplate['default'] ?? null,
                        $fieldTemplate['templateForCreate'] ?? null,
                        $fieldTemplate['templateForDrop'] ?? null,
                    );
                } else {
                    $nullable = !(isset($fieldWithParamsArray[2]) && $fieldWithParamsArray[2] == 'notNull');
                    $foreignUuid = isset($fieldWithParamsArray[3]) && filter_var($fieldWithParamsArray[3], FILTER_VALIDATE_BOOLEAN);
                    $foreignId = isset($fieldWithParamsArray[4]) && filter_var($fieldWithParamsArray[4], FILTER_VALIDATE_BOOLEAN);
                    $default = $fieldWithParamsArray[5] ?? null;
                    $columns[] = new ColumnMigrationDTO(
                        $fieldWithParamsArray[0],
                        $fieldWithParamsArray[1] ?? 'string',
                        $nullable,
                        $foreignUuid,
                        $foreignId,
                        $default
                    );
                }
            }
        }

        return $columns;
    }

    private function getTableName($migrationName): string
    {
        $migrationName = str_replace('_table', '', $migrationName);
        if (str_contains($migrationName, 'add_')) {
            return substr($migrationName, strpos($migrationName, '_to_') + 4);
        }
        return str_replace('create_', '', $migrationName);
    }

    /**
     * @param array|string|null $migrationName
     *
     * @return void
     */
    public function makeMigrationByLaravel(array|string|null $migrationName): void
    {
        $lastMigrationFilename = $this->getLastMigrationFilename();
        if (str_contains($lastMigrationFilename, $migrationName)) {
            $this->info('Migration has been created');
        } else {
            $this->info('Start create migration');
            $start_time = microtime(true);
            $result = $this->callSilently("make:migration", ['name' => $migrationName]);
            $end_time = microtime(true);
            $total_time = $end_time - $start_time;
            $this->info("Время выполнения: " . $total_time . " секунд");
            $this->info("Migration status = $result");
        }
    }

    /**
     * @param int $offset
     *
     * @return string|null
     */
    public function getLastMigrationFilename(int $offset = 1): ?string
    {
        $path = $this->getMigrationsPath();
        $files = scandir($path);
        if (count($files)) {
            return $files[count($files) - $offset];
        }
        return null;
    }

    /**
     * @return string
     */
    public function getMigrationsPath(): string
    {
        return database_path('/migrations'); // укажите свой путь к папке
    }
}
