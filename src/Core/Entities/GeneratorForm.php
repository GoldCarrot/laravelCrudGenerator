<?php

namespace Chatway\LaravelCrudGenerator\Core\Entities;

use Chatway\LaravelCrudGenerator\Core\DTO\ControllerParams;
use Chatway\LaravelCrudGenerator\Core\DTO\EnumParams;
use Chatway\LaravelCrudGenerator\Core\DTO\MainParams;
use Chatway\LaravelCrudGenerator\Core\DTO\PropertyDTO;
use Chatway\LaravelCrudGenerator\Core\DTO\RouteParams;
use Chatway\LaravelCrudGenerator\Core\Helpers\ConsoleHelper;
use Chatway\LaravelCrudGenerator\Core\Helpers\DB\ColumnService;
use Chatway\LaravelCrudGenerator\Core\Helpers\DB\ForeignKeyService;
use Chatway\LaravelCrudGenerator\Core\Helpers\ClassHelper;
use Chatway\LaravelCrudGenerator\Core\Templates\Routes\GeneratorRouteTemplates;
use ReflectionException;
use Str;

/**
 * @property string                $baseNs
 * @property string                $resourceName
 * @property PropertyDTO []        $properties
 * @property EnumParams []         $enums
 * @property ControllerParams []   $controllers
 * @property RouteParams [] | array $routeTemplates
 */
class GeneratorForm
{
    public static string $MODEL_FOLDER_NAME      = 'Entities';
    public static string $REPOSITORY_FOLDER_NAME = 'Repositories';
    public static string $ENUM_FOLDER_NAME       = 'Enums';
    public static string $VIEW_FILE_SUFFIX       = '.blade.php';
    public static string $SERVICE_FOLDER_NAME    = 'Services';
    public static string $PRESENTER_FOLDER_NAME  = 'Presenters';

    public static string $PRESENTER_SUFFIX  = 'Presenter';
    public static string $SERVICE_SUFFIX    = 'Service';
    public static string $REPOSITORY_SUFFIX = 'Repository';
    public static string $CONTROLLER_SUFFIX = 'Controller';

    public string $resourceTable;
    public string $resourceName;

    public string $modelName;
    public array  $controllers = [];
    public string $repositoryName;
    public string $serviceName;
    public string $enumName;
    public string $presenterName;

    public string $baseNs    = 'App\Domain';
    public string $httpNs    = 'App\Http\Admin';
    public string $httpApiNs = 'App\Http\Api';
    public string $folderNs  = '';

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
    public string $viewsPath;
    /** Параметры View конец */

    /** Параметры Route начало */
    public array $routeTemplates = [];
    /** Параметры Route конец */

    /** Общие параметры начало */
    public bool   $force;
    public string $mainPath;
    public bool   $testMode     = false;
    public array  $generateList = [];
    public string  $action;
    /** Общие параметры конец */

    protected ForeignKeyService $foreignKeyService;

    public function __construct(MainParams $mainParams, ForeignKeyService $foreignKeyService)
    {
        $this->enums = $mainParams->enums;
        $this->generateList = $mainParams->generateList;
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

        $this->modelName = $this->baseNs . $this->folderNs . '\\' . self::$MODEL_FOLDER_NAME . '\\' . $this->resourceName;
        $controller = new ControllerParams([
            'controllerName' => $this->httpNs . 'Controllers\\' . $this->folderNs . '\\' . $this->resourceName
                                . self::$CONTROLLER_SUFFIX,
            'templateName'   => 'controllerAdmin',
            'baseClass'      => GeneratorForm::getSafeEnv('GENERATOR_ADMIN_CONTROLLER_EXTENDS') ??
                                'App\Http\Admin\Controllers\ResourceController',
        ]);

        $this->controllers['controllerAdmin'] = $controller;
        $controller = new ControllerParams([
            'controllerName' => $this->httpApiNs . 'Controllers\\' . $this->folderNs . '\\' . $this->resourceName
                                . self::$CONTROLLER_SUFFIX,
            'templateName'   => 'controllerApi',
            'baseClass'      => GeneratorForm::getSafeEnv('GENERATOR_API_CONTROLLER_EXTENDS') ?? 'App\Http\Api\Controllers\Controller',
        ]);
        $this->controllers['controllerApi'] = $controller;
        $this->repositoryName = $this->baseNs . $this->folderNs . '\\' . self::$REPOSITORY_FOLDER_NAME . '\\' . $this->resourceName
                                . self::$REPOSITORY_SUFFIX;
        $this->serviceName =
            $this->baseNs . $this->folderNs . '\\' . self::$SERVICE_FOLDER_NAME . '\\' . $this->resourceName . self::$SERVICE_SUFFIX;
        $this->presenterName = $this->httpApiNs . self::$PRESENTER_FOLDER_NAME . '\\' . $this->folderNs . '\\' . $this->resourceName
                               . self::$PRESENTER_SUFFIX;


        $this->viewsPath = self::getSafeEnv('GENERATOR_VIEWS_PATH') ??
                           'views\admin\\' . Str::pluralStudly(lcfirst(class_basename($this->resourceName)));

        foreach ($this->enums as $enum) {
            $enum->enumName = $this->baseNs . $this->folderNs . '\\' . self::$ENUM_FOLDER_NAME . '\\' . $this->resourceName
                              . ucfirst($enum->name) . 'Enum';
        }

        $this->routeTemplates = (new GeneratorRouteTemplates())->getRoutes();

        if ($mainParams->previewPaths) {
            if (count($this->generateList) == 0 || in_array('model', $this->generateList)) {
                ConsoleHelper::info($this->modelName);
            }
            if (count($this->generateList) == 0 || in_array('controllers', $this->generateList)) {
                foreach ($this->controllers as $controller) {
                    ConsoleHelper::info($controller->controllerName);
                }
            }
            if (count($this->generateList) == 0 || in_array('presenter', $this->generateList)) {
                ConsoleHelper::info($this->presenterName);
            }
            if (count($this->generateList) == 0 || in_array('repository', $this->generateList)) {
                ConsoleHelper::info($this->repositoryName);
            }
            if (count($this->generateList) == 0 || in_array('service', $this->generateList)) {
                ConsoleHelper::info($this->serviceName);
            }
            if (count($this->generateList) == 0 || in_array('enums', $this->generateList)) {
                foreach ($this->enums as $enum) {
                    ConsoleHelper::info($enum->enumName);
                }
            }
            if (count($this->generateList) == 0 || in_array('views', $this->generateList)) {
                ConsoleHelper::info($this->viewsPath);
            }
        }
        $this->force = $mainParams->force;
        $this->mainPath = $mainParams->mainPath;
    }

    private function initEnv()
    {
        $this->testMode = self::getSafeEnv('GENERATOR_TEST_MODE') ?? false;
        self::$CONTROLLER_SUFFIX = self::getSafeEnv('GENERATOR_CONTROLLER_SUFFIX') ?? self::$CONTROLLER_SUFFIX;
        self::$PRESENTER_SUFFIX = self::getSafeEnv('GENERATOR_PRESENTER_SUFFIX') ?? self::$PRESENTER_SUFFIX;
        self::$PRESENTER_SUFFIX = self::getSafeEnv('GENERATOR_PRESENTER_SUFFIX') ?? self::$PRESENTER_SUFFIX;
        self::$REPOSITORY_SUFFIX = self::getSafeEnv('GENERATOR_REPOSITORY_SUFFIX') ?? self::$REPOSITORY_SUFFIX;

        self::$MODEL_FOLDER_NAME = self::getSafeEnv('GENERATOR_MODEL_FOLDER_NAME') ?? self::$MODEL_FOLDER_NAME;
        self::$REPOSITORY_FOLDER_NAME = self::getSafeEnv('GENERATOR_REPOSITORY_FOLDER_NAME') ?? self::$REPOSITORY_FOLDER_NAME;

        $this->baseNs = self::getSafeEnv('GENERATOR_BASE_NS') ?? $this->baseNs;
        $this->httpNs = self::getSafeEnv('GENERATOR_HTTP_NS') ?? $this->httpNs;
        $this->httpApiNs = self::getSafeEnv('GENERATOR_HTTP_API_NS') ?? $this->httpApiNs;
    }

    public static function getSafeEnv($parameterName)
    {
        return env($parameterName) ? env($parameterName) : null;
    }

    public function getNsByClassName($className): string
    {
        return join("\\", array_slice(explode("\\", $className), 0, -1));
    }

    public function setResourceTable($resourceTable)
    {
        $this->resourceTable = $resourceTable;
        $this->generateProperties($resourceTable);
        try {
            $this->generateInternalForeignKeys($resourceTable);
        } catch (ReflectionException $e) {
            dd($e->getMessage(), $e->getTraceAsString());
        }
        try {
            $this->generateExternalForeignKeys($resourceTable);
        } catch (ReflectionException $e) {
            dd($e->getMessage(), $e->getTraceAsString());
        }
        $this->solveTextSpaceForProperties();
    }

    /**
     * Description Извлекаем все внешние ключи
     * эту таблицу
     *
     * @param $tableName
     *
     * @throws ReflectionException
     */
    private function generateInternalForeignKeys($tableName)
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
     * @throws ReflectionException
     */
    private function generateExternalForeignKeys($tableName)
    {
        $keys = [];
        $foreignKeys = $this->foreignKeyService->getExternalKeys($tableName);
        $classes = ClassHelper::getAllEntitiesInProject();
        foreach ($foreignKeys as $foreignKey) {
            $foreignExtKeys = $this->foreignKeyService->getInternalKeys($foreignKey->TABLE_NAME);
            foreach ($foreignExtKeys as $foreignExtKey) {
                $index = array_search($foreignExtKey->REFERENCED_TABLE_NAME, array_column($classes, 'tableName'));
                if ($foreignExtKey->REFERENCED_TABLE_NAME != $tableName && $index != false) {
                    $keys[] = array_replace($classes[$index], ['tableName' => $foreignExtKey->TABLE_NAME]);
                }
            }
        }
        $this->externalForeignKeys = $keys;
    }

    private function generateProperties($tableName)
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
                'type'     => $type,
                'name'     => $column->Field,
                'nullable' => $column->Null != 'NO',
                'isEnum'   => in_array($column->Field, array_keys($this->enums)),
            ];
            $this->properties[$column->Field] = new PropertyDTO($data);
            if ($this->properties[$column->Field]->isEnum) {
                $this->properties[$column->Field]->enum = $this->enums[$column->Field];
            }
            $this->columns[] = $column->Field;
        }
    }

    private function solveTextSpaceForProperties()
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
        return $type . str_repeat(' ', $this->spaceForProperties - strlen($type)) . ' $' . $name;
    }

    public function getResourceName($plural = false, $lowFirstSymbol = false): string
    {
        $resourceName = $lowFirstSymbol ? lcfirst($this->resourceName) : $this->resourceName;
        return $plural ? Str::pluralStudly($resourceName) : $resourceName;
    }
}
