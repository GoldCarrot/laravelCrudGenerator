<?php

namespace Chatway\LaravelCrudGenerator\Core\Entities;

use App\Console\Generator\DTO\PropertyDTO;
use App\Console\Generator\Helpers\DB\ColumnService;
use App\Console\Generator\Helpers\DB\ForeignKeyService;
use App\Console\Generator\Helpers\ClassHelper;
use ReflectionException;
use Str;

/**
 * @property string $baseNs
 * @property string $resourceName
 * @property PropertyDTO [] $properties
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
    public $enumStatusName;

    public $baseClassNs = 'App\Base\Models\\';
    public $baseClass   = 'AbstractModel';

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
    public $enumPostfix = 'Status';

    public $enumList = [];
    /** Параметры Enum конец */
    /**
     * @var ForeignKeyService
     */
    protected $foreignKeyService;

    const TEST_MODE = false;

    public function __construct($data, ForeignKeyService $foreignKeyService)
    {
        $this->foreignKeyService = $foreignKeyService;
        $this->setResourceTable(\Arr::get($data, 'resourceTable'));
        $this->resourceName = \Arr::get($data, 'resourceName');
        $this->baseNs = $data['baseNs'] ?? $this->baseNs . '\\' . (self::TEST_MODE ? 'Test' : $this->resourceName) . '\\';
        $this->httpNs = $data['httpNs'] ?? $this->httpNs . '\\' . (self::TEST_MODE ? 'Test' : $this->resourceName) . '\\';
        if (substr($this->httpNs, -1, 1) != '\\') {
            $this->httpNs .= '\\';
        }
        if (substr($this->baseNs, -1, 1) != "\\") {
            $this->baseNs .= '\\';
        }
        $this->modelName = $this->baseNs . self::MODEL_FOLDER_NAME . '\\' . $this->resourceName;
        $this->controllerName = $this->httpNs . $this->resourceName . self::CONTROLLER_SUFFIX;
        $this->repositoryName = $this->baseNs . self::REPOSITORY_FOLDER_NAME . '\\' . $this->resourceName . self::REPOSITORY_SUFFIX;
        $this->serviceName = $this->baseNs . self::SERVICE_FOLDER_NAME . '\\' . $this->resourceName . self::SERVICE_SUFFIX;
        $this->enumStatusName = $this->baseNs . self::ENUM_FOLDER_NAME . '\\' . $this->resourceName . 'Status';
    }

    public function getNsByClassName($className): string
    {
        return str_replace('\\' . class_basename($className), '', $className);
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
        $columns = \DB::select('show columns from ' . $tableName);


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
            if ($column->Null != 'NO') {
                //$type .= '|null';
            }
            $data = [
                'type'     => $type,
                'name'     => $column->Field,
                'nullable' => $column->Null != 'NO',
            ];
            //$this->properties[$column->Field] = $data;
            $this->properties[$column->Field] = new PropertyDTO($data);
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
            if ($this->spaceForProperties < strlen(basename($property['className']))) {
                $this->spaceForProperties = strlen(basename($property['className']));
            }
        }
        foreach ($this->extrernalForeignKeys as $property) {
            if ($this->spaceForProperties < strlen(basename($property['className']) . ' []')) {
                $this->spaceForProperties = strlen(basename($property['className']) . ' []');
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
