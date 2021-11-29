<?php

namespace Chatway\LaravelCrudGenerator\Commands;

use Chatway\LaravelCrudGenerator\Core\DTO\MainParams;
use Chatway\LaravelCrudGenerator\Core\GeneratorHandler;
use DB;
use Illuminate\Console\Command;

class GeneratorCommand extends Command
{
    protected $signature = 'gen:all 
    {table : Таблица в БД} 
    {baseNs? : Базовый namespace (entities, repositories, services...)} 
    {httpNs? : Namespace для контроллера}
    {--def-status-off : Генерация Enum Status со стандартными текстовыми статусами active, inactive, deleted }
    {--enum : Генерация Enum файлов, пример: ="type-sport,home,work;status-active,inactive,deleted"}
    {--force : Удаляет файлы, и записывает новые, иначе пропускаются файлы}
    {--previewPaths : Показывает все пути }
    ';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $tableName = $this->argument('table');
        $tables = \Arr::pluck(DB::select('SHOW TABLES'), "Tables_in_" . config('database.connections.mysql.database'));
        if (in_array($tableName, $tables)) {
            $data =
                [
                    'resourceTable'         => $tableName,
                    'baseNs'                => $this->argument('baseNs'),
                    'httpNs'                => $this->argument('httpNs'),
                    'defaultStatusGenerate' => !$this->option('def-status-off'),
                    'enumParams'            => $this->option('enum'),
                    'previewPaths'          => (bool)$this->option('previewPaths'),
                    'force'                 => (bool)$this->option('force'),
                    'mainPath'                 => dirname(__DIR__),
                ];
            try {
                (new GeneratorHandler())->start(new MainParams($data));
            } catch (\Throwable $e) {
                dd($e->getMessage(), $e->getTraceAsString());
            }
        } else {
            $this->error("\nТаблицы $tableName не существует\n");
        }
        return 0;
    }
}
