<?php

namespace Chatway\LaravelCrudGenerator\Core\DTO;

use App;
use Arr;
use Chatway\LaravelCrudGenerator\Core\Enums\ScenariosEnum;
use Chatway\LaravelCrudGenerator\Core\Helpers\ConsoleHelper;
use Chatway\LaravelCrudGenerator\Core\Helpers\DB\ColumnService;
use Str;

/**
 * @property EnumParams[] $enums
 */
class MainParams
{
    public mixed  $resourceTable;
    public string $resourceName;
    public mixed  $folderNs;
    public array  $enums;
    public bool   $force;
    public string $mainPath;
    public ?string       $action;
    public ?string       $scenario;
    public ScenariosEnum $scenariosEnum;

    public function __construct($data)
    {
        $this->resourceTable = Arr::get($data, 'resourceTable');
        $this->resourceName = ucfirst(Str::camel(Str::singular($this->resourceTable)));
        $this->folderNs = Arr::get($data, 'folderNs');
        $this->enums = $this->getEnums(Arr::get($data, 'enumParams'), Arr::get($data, 'defaultStatusGenerate', false));
        $this->force = Arr::get($data, 'force', false);
        $this->mainPath = Arr::get($data, 'mainPath');
        $this->action = Arr::get($data, 'action') ?? 'generate';
        $this->scenariosEnum = Arr::get($data, 'scenariosEnum', App::make(ScenariosEnum::class));
        $this->scenario = Arr::get($data, 'scenario', ScenariosEnum::DEFAULT);
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
            //проверяем наличие столбца status, если есть то генерируем стандартный enum
            if (in_array('status', $columns)) {
                $enums['status'] = new EnumParams('status-', $defaultValues, true);
            }
        }
        return $enums;
    }
}
