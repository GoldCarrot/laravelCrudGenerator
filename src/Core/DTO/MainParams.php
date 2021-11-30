<?php

namespace Chatway\LaravelCrudGenerator\Core\DTO;

use Chatway\LaravelCrudGenerator\Core\Helpers\ConsoleHelper;
use Chatway\LaravelCrudGenerator\Core\Helpers\DB\ColumnService;

/**
 * @property EnumParams[] $enums
 */
class MainParams
{
    public mixed  $resourceTable;
    public string $resourceName;
    public mixed  $folderNs;
    public array  $enums;
    public       $previewPaths;
    public       $force;
    public       $mainPath;

    public function __construct($data)
    {
        $this->resourceTable = \Arr::get($data, 'resourceTable');
        $this->resourceName = ucfirst(\Str::camel($this->resourceTable));
        $this->folderNs = \Arr::get($data, 'folderNs');
        $this->enums = $this->getEnums(\Arr::get($data, 'folderNs'), \Arr::get($data, 'defaultStatusGenerate', false));
        $this->previewPaths = \Arr::get($data, 'previewPaths', false);
        $this->force = \Arr::get($data, 'force', false);
        $this->mainPath = \Arr::get($data, 'mainPath', null);
        if (!$this->mainPath) {
            ConsoleHelper::error('Main path is not null');
            die;
        }
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
