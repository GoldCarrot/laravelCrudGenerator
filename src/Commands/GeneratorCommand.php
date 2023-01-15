<?php

namespace Chatway\LaravelCrudGenerator\Commands;

use Arr;
use Chatway\LaravelCrudGenerator\Core\DTO\MainParams;
use Chatway\LaravelCrudGenerator\Core\Enums\ScenariosEnum;
use Chatway\LaravelCrudGenerator\Core\GeneratorHandler;
use DB;
use Throwable;

class GeneratorCommand extends BaseCommand
{
    protected $signature = '
    generate
    {table : Table name in DB}
    {folderNs? : Base namespace folder \App\Domain\{folderNs}\[Entities,repositories]}
    {--scenario=default : Scenario, create your custom class for generate custom list files"}
    {--action=generate : Action, example generate - generate files (default); rollback - delete generated files and folders (if empty) }
    {--def-status-off : Generate Enum Status with default text statuses active, inactive, deleted }
    {--enum= : Generate Enum files, example: ="type-sport,home,work;status-active,inactive,deleted"}
    {--force : Delete and write new files, if off this parameter, then skip files}
    ';
    public function __construct(
        private ScenariosEnum $scenariosEnum
    )
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $tableName = $this->argument('table');
        $tables = Arr::pluck(DB::select('SHOW TABLES'), "Tables_in_" . config('database.connections.mysql.database'));
        if (in_array($tableName, $tables)) {
            $data =
                [
                    'resourceTable'         => $tableName,
                    'folderNs'              => $this->argument('folderNs'),
                    'defaultStatusGenerate' => !$this->option('def-status-off'),
                    'enumParams'            => $this->option('enum'),
                    'force'                 => (bool)$this->option('force'),
                    'mainPath'              => dirname(__DIR__),
                    'action'                => $this->option('action'),
                    'scenario'              => $this->option('scenario'),
                    'scenariosEnum'         => $this->scenariosEnum,
                ];
            try {
                (new GeneratorHandler())->start(new MainParams($data));
            } catch (Throwable $e) {
                dd($e->getMessage(), $e->getTraceAsString());
            }
        } else {
            $this->error("Table $tableName not exists in DB");
        }
        return 0;
    }
}
