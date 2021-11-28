<?php

namespace Chatway\LaravelCrudGenerator\Core\DTO;

use Chatway\LaravelCrudGenerator\Core\Helpers\DB\ColumnService;

/**
 * @property array|\ArrayAccess|mixed $resourceTable
 * @property string                   $resourceName
 * @property array|\ArrayAccess|mixed $baseNs
 * @property array|\ArrayAccess|mixed $httpNs
 * @property EnumParams[]             $enums
 */
class MainParams
{
    public $resourceTable;
    public $resourceName;
    public $baseNs;
    public $httpNs;
    public $enums;
    public $previewPaths;
    public $force;

    public function __construct($data)
    {
        $this->resourceTable = \Arr::get($data, 'resourceTable');
        $this->resourceName = ucfirst(\Str::camel($this->resourceTable));
        $this->baseNs = \Arr::get($data, 'baseNs');
        $this->httpNs = \Arr::get($data, 'httpNs');
        $this->enums = $this->getEnums(\Arr::get($data, 'baseNs'), \Arr::get($data, 'defaultStatusGenerate', false));
        $this->previewPaths = \Arr::get($data, 'previewPaths', false);
        $this->force = \Arr::get($data, 'force', false);
    }

    /**
     * @return EnumParams []
     */
    private function getEnums($enumParams, $defaultStatusGenerate): array
    {
        $defaultValues = env('GENERATOR_DEFAULT_ENUM_VALUES', ['active', 'inactive', 'deleted']);

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
            $columns = ColumnService::getColumnsByTableName($this->resourceTable);
            $columns = array_column($columns, 'Field');

            if (in_array('status', $columns)) {
                $enums['status'] = new EnumParams('status-', $defaultValues);
            }
        }
        return $enums;
    }
}
