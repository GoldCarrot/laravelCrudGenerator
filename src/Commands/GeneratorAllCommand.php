<?php

namespace Chatway\LaravelCrudGenerator\Commands;

use Chatway\LaravelCrudGenerator\Core\DTO\MainParams;
use Chatway\LaravelCrudGenerator\Core\GeneratorHandler;
use DB;
use Illuminate\Console\Command;

class GeneratorAllCommand extends Command
{
    protected $signature = 'gen:all
    {table : Table name in DB}
    {folderNs? : Base namespace folder \App\Domain\{folderNs}\[Entities,repositories]}
    {--def-status-off : Generate Enum Status with default text statuses active, inactive, deleted }
    {--enum= : Generate Enum files, example: ="type-sport,home,work;status-active,inactive,deleted"}
    {--force : Delete and write new files, if off this parameter, then skip files}
    {--previewPaths : View all paths files, generate off }
    {--generateList= : Generate file list, if empty, then generate all files }
    {--action= : Action, example generate - generate files (default); rollback - delete generated files and folders (if empty) }
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
                    'generateList'          => $this->option('generateList') ? explode(',', $this->option('generateList')) : [],
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
            $this->error("\nTable $tableName not exists in DB\n");
        }
        return 0;
    }
}
