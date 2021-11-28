<?php

namespace Chatway\LaravelCrudGenerator\Core\Entities;

use Chatway\LaravelCrudGenerator\Core\DTO\EnumParams;
use Chatway\LaravelCrudGenerator\Core\DTO\MainParams;
use Chatway\LaravelCrudGenerator\Core\DTO\PropertyDTO;
use Chatway\LaravelCrudGenerator\Core\Generators\EnumGenerator;
use Chatway\LaravelCrudGenerator\Core\Helpers\ConsoleHelper;
use Chatway\LaravelCrudGenerator\Core\Helpers\DB\ColumnService;
use Chatway\LaravelCrudGenerator\Core\Helpers\DB\ForeignKeyService;
use Chatway\LaravelCrudGenerator\Core\Helpers\ClassHelper;
use ReflectionException;
use Str;

/**
 * @property string         $baseNs
 * @property string         $resourceName
 * @property PropertyDTO [] $properties
 * @property EnumParams []  $enums
 */
class GeneratorForm
{
    const MODEL_FOLDER_NAME      = 'Entities';
    const REPOSITORY_FOLDER_NAME = 'Repositories';
    const ENUM_FOLDER_NAME       = 'Enums';
    const SERVICE_FOLDER_NAME    = 'Services';
    const REPOSITORY_SUFFIX      = 'Repository';
    const CONTROLLER_SUFFIX      = 'Controller';
    const SERVICE_SUFFIX         = 'Service';
    const ENUM_STATUS_SUFFIX     = 'Status';
    const VIEW_FILE_SUFFIX       = '.blade.php';

    public $resourceTable;

    public $resourceName;

    public $modelName;
    public $controllerName;
    public $repositoryName;
    public $serviceName;
    public $enumName;

    public $baseNs = 'App\Domain';
    public $httpNs = 'App\Http\Admin\Controllers';

    /** Свойства и ключи модели начало */
    public $properties;
    public $columns;
    public $carbonIsset          = false;
    public $statusIsset          = false;
    public $internalForeignKeys  = [];
    public $extrernalForeignKeys = [];
    public $spaceForProperties   = 0;
    public $dateProperties       = [];
    /** Свойства и ключи модели конец */

    /** Параметры Enum начало */

    public $enums = [];
    /** Параметры Enum конец */

    /** Параметры View начало */
    public $viewsPath;
    /** Параметры View конец */

    /** Общие параметры начало */
    public $force;
    public $mainPath;
    public $testMode = false;
    /** Общие параметры конец */
    /**
     * @var ForeignKeyService
     */
    protected $foreignKeyService;

    public function __construct(MainParams $mainParams, ForeignKeyService $foreignKeyService)
    {
        $this->testMode = self::getSafeEnv('GENERATOR_TEST_MODE') ?? false;
        $this->enums = $mainParams->enums;
        $this->foreignKeyService = $foreignKeyService;
        $this->setResourceTable($mainParams->resourceTable);
        $this->resourceName = $mainParams->resourceName;

        $this->baseNs = self::getSafeEnv('GENERATOR_BASE_NS') ??
                        ($mainParams->baseNs ?? $this->baseNs . '\\' . ($this->testMode ? 'Test' : $this->resourceName) . '\\');

        $this->httpNs = self::getSafeEnv('GENERATOR_HTTP_NS') ??
                        ($mainParams->httpNs ?? $this->httpNs . '\\' . ($this->testMode ? 'Test' : $this->resourceName) . '\\');

        if (substr($this->httpNs, -1, 1) != '\\') {
            $this->httpNs .= '\\';
        }
        if (substr($this->baseNs, -1, 1) != "\\") {
            $this->baseNs .= '\\';
        }

        $this->modelName =
            $this->baseNs . (self::getSafeEnv('GENERATOR_MODEL_FOLDER_NAME') ?? self::MODEL_FOLDER_NAME) . '\\' . $this->resourceName;
        $this->controllerName =
            $this->httpNs . $this->resourceName . (self::getSafeEnv('GENERATOR_CONTROLLER_SUFFIX') ?? self::CONTROLLER_SUFFIX);
        $this->repositoryName =
            $this->baseNs . (self::getSafeEnv('GENERATOR_REPOSITORY_FOLDER_NAME') ?? self::REPOSITORY_FOLDER_NAME) . '\\'
            . $this->resourceName . (self::getSafeEnv('GENERATOR_REPOSITORY_SUFFIX') ?? self::REPOSITORY_SUFFIX);
        $this->serviceName = $this->baseNs . (self::getSafeEnv('GENERATOR_SERVICE_FOLDER_NAME') ?? self::SERVICE_FOLDER_NAME) . '\\'
                             . $this->resourceName . (self::getSafeEnv('GENERATOR_SERVICE_SUFFIX') ?? self::SERVICE_SUFFIX);
        $this->viewsPath = self::getSafeEnv('GENERATOR_VIEWS_PATH') ??
                           'views\admin\\' . Str::pluralStudly(lcfirst(class_basename($this->resourceName)));

        foreach ($this->enums as $enum) {
            $enum->enumName = $this->baseNs . self::ENUM_FOLDER_NAME . '\\' . $this->resourceName
                              . ucfirst($enum->name);
        }

        if ($mainParams->previewPaths) {
            ConsoleHelper::info($this->modelName);
            ConsoleHelper::info($this->controllerName);
            ConsoleHelper::info($this->repositoryName);
            ConsoleHelper::info($this->serviceName);
            foreach ($this->enums as $enum) {
                ConsoleHelper::info($enum->enumName);
            }
            ConsoleHelper::info($this->viewsPath);
        }
        $this->force = $mainParams->force;
        $this->mainPath = $mainParams->mainPath;
    }

    private static function getSafeEnv($parameterName)
    {
        return env($parameterName) ? env($parameterName) : null;
    }

    public function getNsByClassName($className): string
    {
        return join("\\", array_slice(explode("\\", $className), 0, -1));
    }

    public function getBaseClassWithNs()
    {
        return $this->baseClassNs . $this->baseClass;
    }

    public function setResourceTable($resourceTable)
    {
        $this->resourceTable = $resourceTable;
        $this->generateProperties($resourceTable);
        $this->generateInternalForeignKeys($resourceTable);
        $this->generateExternalForeignKeys($resourceTable);
        $this->solveTextSpaceForProperties();
    }

    /**
     * Description Извлекаем все внешние ключи //todo написать лучше две функции: ключи внутри таблицы, и ключи которые ссылаются на
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
     * Description Извлекаем все внешние ключи //todo написать лучше две функции: ключи внутри таблицы, и ключи которые ссылаются на
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
        $this->extrernalForeignKeys = $keys;
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
                    if (strpos($column->Type, 'char') !== false) {
                        $type = 'string';
                    } elseif (strpos($column->Type, 'mediumtext') !== false) {
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
        foreach ($this->extrernalForeignKeys as $property) {
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
    public function getFormattedProperty($type, $name)
    {
        return $type . str_repeat(' ', $this->spaceForProperties - strlen($type)) . ' $' . $name;
    }

    public function getModelNs()
    {
        return $this->baseNs . $this::MODEL_FOLDER_NAME;
    }

    public function getModelName()
    {
        return $this->resourceName;
    }

    public function getModelFullName()
    {
        return $this->getModelNs() . '\\' . $this->getModelName();
    }

    public function getRepositoryNs()
    {
        return $this->baseNs . $this::REPOSITORY_FOLDER_NAME;
    }

    public function getRepositoryName()
    {
        return $this->resourceName . self::REPOSITORY_SUFFIX;
    }

    public function getRepositoryFullName()
    {
        return $this->getRepositoryNs() . '\\' . $this->getRepositoryName();
    }

    public function getServiceNs()
    {
        return $this->baseNs . $this::SERVICE_FOLDER_NAME;
    }

    public function getServiceName()
    {
        return $this->resourceName . self::SERVICE_SUFFIX;
    }

    public function getServiceFullName()
    {
        return $this->getServiceNs() . '\\' . $this->getServiceName();
    }

    public function getEnumNs()
    {
        return $this->baseNs . $this::ENUM_FOLDER_NAME;
    }

    public function getEnumName()
    {
        return $this->resourceName . self::ENUM_STATUS_SUFFIX;
    }

    public function getEnumFullName()
    {
        return $this->getEnumNs() . '\\' . $this->getEnumName();
    }

    public function getResourceName($plural = false, $lowFirstSymbol = false)
    {
        $resourceName = $lowFirstSymbol ? lcfirst($this->resourceName) : $this->resourceName;
        return $plural ? Str::pluralStudly($resourceName) : $resourceName;
    }
}
