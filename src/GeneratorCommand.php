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
use Chatway\LaravelCrudGenerator\Core\Entities\GeneratorForm;
use Chatway\LaravelCrudGenerator\Core\Entities\ModelForm;
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
    public $tableName;
    protected     $signature = 'gen:all 
    {table : Таблица в БД} 
    {baseNs? : Базовый namespace (entities, repositories, services...)} 
    {httpNs? : Namespace для контроллера}
    {--def-status-off : Генерация Enum Status со стандартными текстовыми статусами active, inactive, deleted }
    {--enum : ="type-sport,home,work;status-active,inactive,deleted"}
    ';

    public function __construct()
    {
        self::$MAIN_PATH = __DIR__;
        parent::__construct();
    }

    public function handle(): int
    {
        $resourceTable = $this->argument('table');
        $tables = \Arr::pluck(DB::select('SHOW TABLES'), "Tables_in_" . config('database.connections.mysql.database'));
        if (in_array($resourceTable, $tables)) {
            $this->tableName = ucfirst(\Str::camel($resourceTable));
            $baseNs = $this->argument('baseNs');
            $httpNs = $this->argument('httpNs');
            $data =
                ['resourceTable' => $resourceTable, 'resourceName' => $this->tableName, 'baseNs' => $baseNs, 'httpNs' => $httpNs, 'enums' => $this->getEnums()];
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
            foreach ($generatorForm->enums as $enum) {
                $enum->enumName = $generatorForm->baseNs . $generatorForm::ENUM_FOLDER_NAME . '\\' . $generatorForm->resourceName
                        . ucfirst($enum->name);
                $result = (new EnumGenerator($generatorForm, $enum))->generate();
                if ($result->success !== false) {
                    $this->info('Enum generated! Path in app: ' . $result->filePath);
                }
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

    /**
     * @return EnumParams []
     */
    private function getEnums(): array
    {
        $defaultValues = env('GENERATOR_DEFAULT_ENUM_VALUES', ['active', 'inactive', 'deleted']);
        $defaultStatusGenerate = !$this->option('def-status-off');
        $enumParams = $this->option('enum');
        $enums = [];
        if ($enumParams) {
            $enumParams = explode(';', $enumParams);
            foreach ($enumParams as $enumParam) {
                $enumParam = new EnumParams($enumParam, $defaultValues);
                if ($enumParam->name) {
                    $enums[$enumParam->name] = $enumParam;
                }
            }
        }
        if (!isset($enums['status']) && $defaultStatusGenerate) {
            $columns = ColumnService::getColumnsByTableName($this->tableName);
            $columns = array_column($columns, 'Field');
            if (in_array('status', $columns)) {
                $enums['status'] = new EnumParams('status-', $defaultValues);
            }
        }
        return $enums;
    }
}
