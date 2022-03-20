<?php

namespace Chatway\LaravelCrudGenerator\Commands;

use Chatway\LaravelCrudGenerator\Core\DTO\MainParams;
use Chatway\LaravelCrudGenerator\Core\GeneratorHandler;
use DB;
use Illuminate\Console\Command;

class GeneratorControllerCommand extends BaseCommand
{
    public function __construct()
    {
        $this->setSignature(['commandName' => 'gen:controller']);
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
                    'previewPaths'          => (bool)$this->option('previewPaths'),
                    'force'                 => (bool)$this->option('force'),
                    'mainPath'              => dirname(__DIR__),
                    'generateList'          => ['controller'],
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
