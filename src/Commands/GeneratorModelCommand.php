<?php

namespace Chatway\LaravelCrudGenerator\Commands;

use Chatway\LaravelCrudGenerator\Core\DTO\MainParams;
use Chatway\LaravelCrudGenerator\Core\GeneratorHandler;
use DB;
use Illuminate\Console\Command;

class GeneratorModelCommand extends Command
{
    protected $signature = 'gen:model 
    {table : Таблица в БД} 
    {folderNs? : Базовый namespace папки \App\Domain\{folderNs}\[Entities,repositories]} 
    {--def-status-off : Генерация Enum Status со стандартными текстовыми статусами active, inactive, deleted }
    {--enum : Генерация Enum файлов, пример: ="type-sport,home,work;status-active,inactive,deleted"}
    {--force : Удаляет файлы, и записывает новые, иначе пропускаются файлы}
    {--previewPaths : Показывает все пути }
    {--generateList= : список файлов для генерации, если пустое, то генерится все подряд }
    {--action= : действие выполняемое скриптом generate - генерирует файлы; rollback - удаляет файлы и папки (если пустые) }
    ';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(): int
    {
        //$arguments = $this->arguments();//todo
        $tableName = $this->argument('table');
        $tables = \Arr::pluck(DB::select('SHOW TABLES'), "Tables_in_" . config('database.connections.mysql.database'));
        if (in_array($tableName, $tables)) {
            $data =
                [
                    'resourceTable'         => $tableName,
                    'folderNs'              => $this->argument('folderNs'),
                    'defaultStatusGenerate' => !$this->option('def-status-off'),
                    'enumParams'            => $this->option('enum'),
                    'generateList'          => ['model'],
                    'previewPaths'          => (bool)$this->option('previewPaths'),
                    'force'                 => (bool)$this->option('force'),
                    'mainPath'              => dirname(__DIR__),
                    'action'                => $this->option('action'),
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
