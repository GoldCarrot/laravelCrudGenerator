<?php

namespace Chatway\LaravelCrudGenerator;
//function class_namespace($class)
//{
//    echo 'asd';
//    $class = is_object($class) ? get_class($class) : $class;
//
//    return join("\\", array_slice(explode("\\", $class), 0, -1));
//}
use Chatway\LaravelCrudGenerator\Core\Entities\GeneratorForm;
use Chatway\LaravelCrudGenerator\Core\Entities\ModelForm;
use Chatway\LaravelCrudGenerator\Core\Generators\ControllerGenerator;
use Chatway\LaravelCrudGenerator\Core\Generators\EnumGenerator;
use Chatway\LaravelCrudGenerator\Core\Generators\ModelGenerator;
use Chatway\LaravelCrudGenerator\Core\Generators\RepositoryGenerator;
use Chatway\LaravelCrudGenerator\Core\Generators\ServiceGenerator;
use Chatway\LaravelCrudGenerator\Core\Generators\ViewGenerator;
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
    {--enum=asd;asd : asd123}
    ';
//status;active,inactive,deleted|type;active,deleted
    public function __construct()
    {
        self::$MAIN_PATH = __DIR__;
        parent::__construct();
    }

    public function handle(): int
    {
        //$resourceName = $this->option('enum');
        $resourceTable = $this->argument('table');
        //$this->info($resourceName);
        //return 0;
        $tables = \Arr::pluck(DB::select('SHOW TABLES'), "Tables_in_" . config('database.connections.mysql.database'));
        if (in_array($resourceTable, $tables)) {
            $resourceName = ucfirst(\Str::camel($resourceTable));
            $baseNs = $this->argument('baseNs');
            $httpNs = $this->argument('httpNs');
            $data = ['resourceTable' => $resourceTable, 'resourceName' => $resourceName, 'baseNs' => $baseNs, 'httpNs' => $httpNs];
            $generatorForm = new GeneratorForm($data, new ForeignKeyService());
            if ($generatorForm::TEST_MODE) {
                $this->error('Test mode: ' . $generatorForm->baseNs);
            }

            $result = (new ModelGenerator($generatorForm))->generate();
            if ($result->success !== false) {
                $this->info('Model generated! Path in app: ' . $result->filePath);
            }
            $result = (new ControllerGenerator($generatorForm))->generate();
            if ($result->success !== false) {
                $this->info('Controller generated! Path in app: ' . $result->filePath);
            }
            $result = (new RepositoryGenerator($generatorForm))->generate();
            if ($result->success !== false) {
                $this->info('Repository generated! Path in app: ' . $result->filePath);
            }
            $result = (new ServiceGenerator($generatorForm))->generate();
            if ($result->success !== false) {
                $this->info('Service generated! Path in app: ' . $result->filePath);
            }
            $result = (new EnumGenerator($generatorForm))->generate();
            if ($result->success !== false) {
                $this->info('Enum generated! Path in app: ' . $result->filePath);
            }
            $viewList = ['create', 'form', 'index', 'show', 'update'];
            foreach ($viewList as $item) {
                $result = (new ViewGenerator($generatorForm, ['viewName' => $item]))->generate();
                if ($result->success !== false) {
                    $this->info("View $item generated! Path in app: " . $result->filePath);
                }
            }
        } else {
            $this->error("\nТаблицы $resourceTable не существует\n");
        }
        return 0;
    }
}
