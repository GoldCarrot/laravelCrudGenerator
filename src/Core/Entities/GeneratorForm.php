<?php

namespace Chatway\LaravelCrudGenerator\Core\Entities;

use Chatway\LaravelCrudGenerator\Core\Base\BaseScenario;
use Chatway\LaravelCrudGenerator\Core\DTO\ControllerParams;
use Chatway\LaravelCrudGenerator\Core\DTO\EnumParams;
use Chatway\LaravelCrudGenerator\Core\DTO\MainParams;
use Chatway\LaravelCrudGenerator\Core\DTO\PropertyDTO;
use Chatway\LaravelCrudGenerator\Core\DTO\ScenarioItem;
use Chatway\LaravelCrudGenerator\Core\Helpers\DB\ColumnService;
use Chatway\LaravelCrudGenerator\Core\Helpers\DB\ForeignKeyService;
use Chatway\LaravelCrudGenerator\Core\Helpers\ClassHelper;
use Str;

/**
 * @property string                 $baseNs
 * @property string                 $resourceName
 * @property PropertyDTO []         $properties
 * @property EnumParams []          $enums
 * @property ControllerParams []    $controllers
 * @property ScenarioItem []    $generators
 //* @property RouteParams [] | array $routeTemplates
 */
class GeneratorForm
{
    public static string $MODEL_FOLDER_NAME      = 'Entities';
    public static string $REPOSITORY_FOLDER_NAME = 'Repositories';
    public static string $ENUM_FOLDER_NAME       = 'Enums';
    public static string $VIEW_FILE_SUFFIX       = '.blade.php';
    public static string $SERVICE_FOLDER_NAME    = 'Services';
    public static string $RESOURCE_FOLDER_NAME   = 'Resources';

    public static string $RESOURCE_SUFFIX   = 'Resource';
    public static string $SERVICE_SUFFIX    = 'Service';
    public static string $REPOSITORY_SUFFIX = 'Repository';
    public static string $CONTROLLER_SUFFIX = 'Controller';

    public string $resourceTable;
    public string $resourceName;

    public string $enumName;

    public string $baseNs    = 'App\Domain';
    public string $httpNs    = 'App\Http\Admin';
    public string $httpApiNs = 'App\Http\Api';
    public string $folderNs  = '';

    public array $generators;

    /** Свойства и ключи модели начало */
    public array $properties;
    public array $columns;
    public bool  $carbonIsset         = false;
    public bool  $statusIsset         = false;
    public array $internalForeignKeys = [];
    public array $externalForeignKeys = [];
    public int   $spaceForProperties  = 0;
    public array $dateProperties      = [];
    /** Свойства и ключи модели конец */

    /** Параметры Enum начало */

    public array $enums = [];
    /** Параметры Enum конец */

    /** Параметры View начало */
    //public string $viewsPath;
    /** Параметры View конец */

    /** Параметры Route начало */
    //public array $routeTemplates = [];
    /** Параметры Route конец */

    /** Общие параметры начало */
    public bool   $force;
    public string $mainPath;
    public bool   $testMode     = false;
    public string $action;
    /** Общие параметры конец */

    protected ForeignKeyService $foreignKeyService;

    public function __construct(MainParams $mainParams, ForeignKeyService $foreignKeyService)
    {
        $this->enums = $mainParams->enums;
        $this->foreignKeyService = $foreignKeyService;
        $this->setResourceTable($mainParams->resourceTable);
        $this->resourceName = $mainParams->resourceName;
        $this->folderNs = $mainParams->folderNs ?? $this->resourceName;
        $this->action = $mainParams->action;

        $this->initEnv();

        if (substr($this->httpNs, -1, 1) != '\\') {
            $this->httpNs .= '\\';
        }
        if (substr($this->httpApiNs, -1, 1) != '\\') {
            $this->httpApiNs .= '\\';
        }
        if (substr($this->baseNs, -1, 1) != "\\") {
            $this->baseNs .= '\\';
        }

        /** @var BaseScenario $scenario */
        $scenario = app($mainParams->scenariosEnum->scenarios[$mainParams->scenario], ['generatorForm' => $this]);
        $this->generators = $scenario->init();
        foreach ($this->enums as $enum) {
            if ($enum->isDefaultStatus) {
                $enum->enumName = env('GENERATOR_DEFAULT_STATUS_ENUM') ?? 'App\Domain\Application\Admin\Enums\DefaultStatusEnum';
            } else {
                $enum->enumName = $this->baseNs . $this->folderNs . '\\' . self::$ENUM_FOLDER_NAME . '\\' . $this->resourceName
                                  . ucfirst($enum->name) . 'Enum';
            }
        }

        $this->force = $mainParams->force;
        $this->mainPath = $mainParams->mainPath;
    }

    private function initEnv(): void
    {
        $this->testMode = env('GENERATOR_TEST_MODE') ?? false;
        self::$CONTROLLER_SUFFIX = env('GENERATOR_CONTROLLER_SUFFIX') ?? self::$CONTROLLER_SUFFIX;
        self::$RESOURCE_SUFFIX = env('GENERATOR_RESOURCE_SUFFIX') ?? self::$RESOURCE_SUFFIX;
        self::$REPOSITORY_SUFFIX = env('GENERATOR_REPOSITORY_SUFFIX') ?? self::$REPOSITORY_SUFFIX;

        self::$MODEL_FOLDER_NAME = env('GENERATOR_MODEL_FOLDER_NAME') ?? self::$MODEL_FOLDER_NAME;
        self::$REPOSITORY_FOLDER_NAME = env('GENERATOR_REPOSITORY_FOLDER_NAME') ?? self::$REPOSITORY_FOLDER_NAME;

        $this->baseNs = env('GENERATOR_BASE_NS') ?? $this->baseNs;
        $this->httpNs = env('GENERATOR_HTTP_NS') ?? $this->httpNs;
        $this->httpApiNs = env('GENERATOR_HTTP_API_NS') ?? $this->httpApiNs;
    }

    public function getNsByClassName($className): string
    {
        return join("\\", array_slice(explode("\\", $className), 0, -1));
    }

    public function setResourceTable($resourceTable): void
    {
        $this->resourceTable = $resourceTable;
        $this->generateProperties($resourceTable);
        $this->generateInternalForeignKeys($resourceTable);
        $this->generateExternalForeignKeys($resourceTable);
        $this->solveTextSpaceForProperties();
    }

    /**
     * Description Извлекаем все внешние ключи
     * эту таблицу
     *
     * @param $tableName
     *
     */
    private function generateInternalForeignKeys($tableName): void
    {
        $keys = [];
        $foreignKeys = $this->foreignKeyService->getInternalKeys($tableName);
        $classes = ClassHelper::getAllEntitiesInProject();
        foreach ($foreignKeys as $foreignKey) {
            if (($index = array_search($foreignKey->REFERENCED_TABLE_NAME, array_column($classes, 'tableName'))) !== false) {
                $keys[] = array_replace($classes[$index], ['columnName' => $foreignKey->COLUMN_NAME]);
                if (isset($this->properties[$foreignKey->COLUMN_NAME])) {
                    $this->properties[$foreignKey->COLUMN_NAME]->class = $classes[$index]['className'];
                    $this->properties[$foreignKey->COLUMN_NAME]->classTable = $classes[$index]['tableName'];
                    $this->properties[$foreignKey->COLUMN_NAME]->foreignKeyExists = true;
                }
            } else {
                if (isset($this->properties[$foreignKey->COLUMN_NAME])) {
                    $this->properties[$foreignKey->COLUMN_NAME]->foreignKeyExists = true;
                }
            }
        }
        $this->internalForeignKeys = $keys;
    }

    /**
     * Description Извлекаем все внешние ключи
     * эту таблицу
     *
     * @param $tableName
     *
     */
    private function generateExternalForeignKeys($tableName): void
    {
        $keys = [];
        $foreignKeys = $this->foreignKeyService->getExternalKeys($tableName);
        $classes = ClassHelper::getAllEntitiesInProject();
        foreach ($foreignKeys as $foreignKey) {
            $foreignExtKeys = $this->foreignKeyService->getInternalKeys($foreignKey->TABLE_NAME);
            foreach ($foreignExtKeys as $foreignExtKey) {
                $index = array_search($foreignExtKey->TABLE_NAME, array_column($classes, 'tableName'));
                $pushed = in_array($foreignExtKey->TABLE_NAME, array_column($keys, 'tableName'));
                if ($foreignExtKey->REFERENCED_TABLE_NAME == $tableName && $index !== false && !$pushed) {
                    $keys[] = array_replace($classes[$index], ['tableName' => $foreignExtKey->TABLE_NAME]);
                } elseif ($foreignExtKey->REFERENCED_TABLE_NAME == $tableName && !$pushed) {
                    $className = Str::singular($foreignExtKey->TABLE_NAME);
                    $className = Str::camel($className);
                    $className = ucfirst($className);
                    $keys[] = ['className' => $className, 'tableName' => $foreignExtKey->TABLE_NAME];
                }
            }
        }
        $this->externalForeignKeys = $keys;
    }

    private function generateProperties($tableName): void
    {
        $this->properties = [];
        $this->columns = [];
        $this->carbonIsset = false;
        $columns = ColumnService::getColumnsByTableName($tableName);


        foreach ($columns as $column) {
            switch ($column->Type) {
                case ColumnService::TYPE_BIGINT_UNSIGNED:
                case ColumnService::TYPE_SMALLINT:
                case ColumnService::TYPE_INT:
                case ColumnService::TYPE_INTEGER:
                case ColumnService::TYPE_BIGINT:
                case ColumnService::TYPE_TINYINT:
                    $type = 'int';
                    break;
                case ColumnService::TYPE_BOOLEAN:
                    $type = 'bool';
                    break;
                case ColumnService::TYPE_FLOAT:
                case ColumnService::TYPE_DOUBLE:
                case ColumnService::TYPE_DECIMAL:
                case ColumnService::TYPE_MONEY:
                    $type = 'float';
                    break;
                case ColumnService::TYPE_DATE:
                case ColumnService::TYPE_TIME:
                case ColumnService::TYPE_JSON:
                case ColumnService::TYPE_TEXT:
                    $type = 'string';
                    break;
                case ColumnService::TYPE_TIMESTAMP:
                case ColumnService::TYPE_DATETIME:
                    $type = 'Carbon';
                    $this->carbonIsset = true;
                    if (!in_array($column->Field, ['created_at', 'updated_at'])) {
                        $this->dateProperties[] = $column->Field;
                    }
                    break;
                default:
                    if (str_contains($column->Type, 'char')) {
                        $type = 'string';
                    } elseif (str_contains($column->Type, 'mediumtext')) {
                        $type = 'string';
                    } else {
                        $type = 'notFoundedType:' . $column->Type;
                    }
            }
            $data = [
                'type'        => $type,
                'typeInTable' => $column->Type,
                'name'        => $column->Field,
                'nullable'    => $column->Null != 'NO',
                'isEnum'      => in_array($column->Field, array_keys($this->enums)),
            ];
            $this->properties[$column->Field] = new PropertyDTO($data);
            if ($this->properties[$column->Field]->isEnum) {
                $this->properties[$column->Field]->enum = $this->enums[$column->Field];
            }
            $this->columns[] = $column->Field;
        }
    }

    private function solveTextSpaceForProperties(): void
    {
        $this->spaceForProperties = 0;
        foreach ($this->properties as $property) {
            if ($this->spaceForProperties < strlen($property->type)) {
                $this->spaceForProperties = strlen($property->type);
            }
        }
        foreach ($this->internalForeignKeys as $property) {
            if ($this->spaceForProperties < strlen(class_basename($property['className']))) {
                $this->spaceForProperties = strlen(class_basename($property['className']));
            }
        }
        foreach ($this->externalForeignKeys as $property) {
            if ($this->spaceForProperties < strlen(class_basename($property['className']) . ' []')) {
                $this->spaceForProperties = strlen(class_basename($property['className']) . ' []');
            }
        }
    }

    /**
     * Форматирует строку параметров для класса
     *
     * @param $type
     * @param $name
     *
     * @return string
     */
    public function getFormattedProperty($type, $name): string
    {
        $spaces = $this->spaceForProperties - strlen($type);
        return $type . str_repeat(' ', max($spaces, 0)) . ' $' . $name;
    }

    public function getResourceName($plural = false, $lowFirstSymbol = false, $camel = false): string
    {
        $resourceName = $lowFirstSymbol ? lcfirst($this->resourceName) : $this->resourceName;
        $resourceName = $plural ? Str::pluralStudly($resourceName) : $resourceName;
        return $camel ? Str::camel($resourceName) : $resourceName;
    }
}
