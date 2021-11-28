<?php

namespace Chatway\LaravelCrudGenerator;

//function class_namespace($class)
//{
//    echo 'asd';
//    $class = is_object($class) ? get_class($class) : $class;
//
//    return join("\\", array_slice(explode("\\", $class), 0, -1));
//}
use Chatway\LaravelCrudGenerator\Core\DTO\EnumParams;
use Chatway\LaravelCrudGenerator\Core\DTO\MainParams;
use Chatway\LaravelCrudGenerator\Core\Entities\GeneratorForm;
use Chatway\LaravelCrudGenerator\Core\Entities\ModelForm;
use Chatway\LaravelCrudGenerator\Core\GeneratorHandler;
use Chatway\LaravelCrudGenerator\Core\Generators\ControllerGenerator;
use Chatway\LaravelCrudGenerator\Core\Generators\EnumGenerator;
use Chatway\LaravelCrudGenerator\Core\Generators\ModelGenerator;
use Chatway\LaravelCrudGenerator\Core\Generators\RepositoryGenerator;
use Chatway\LaravelCrudGenerator\Core\Generators\ServiceGenerator;
use Chatway\LaravelCrudGenerator\Core\Generators\ViewGenerator;
use Chatway\LaravelCrudGenerator\Core\Helpers\DB\ColumnService;
use Chatway\LaravelCrudGenerator\Core\Helpers\DB\ForeignKeyService;
use Chatway\LaravelCrudGenerator\Core\Services\GeneratorFormService;
use App\Domain\User\Repositories\UserRepository;
use DB;
use Illuminate\Console\Command;
use Spatie\Permission\Exceptions\RoleDoesNotExist;
use Storage;
use View;

class GeneratorCommand extends Command
{
    public static $MAIN_PATH = '';

    protected $signature = 'gen:all 
    {table : Таблица в БД} 
    {baseNs? : Базовый namespace (entities, repositories, services...)} 
    {httpNs? : Namespace для контроллера}
    {--def-status-off : Генерация Enum Status со стандартными текстовыми статусами active, inactive, deleted }
    {--enum : Генерация Enum файлов, пример: ="type-sport,home,work;status-active,inactive,deleted"}
    {--force : Удаляет файлы, и записывает новые, иначе пропускаются файлы}
    ';

    public function __construct()
    {
        self::$MAIN_PATH = __DIR__;
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
                ];
            (new GeneratorHandler())->start(new MainParams($data));
        } else {
            $this->error("\nТаблицы $tableName не существует\n");
        }
        return 0;
    }
}
