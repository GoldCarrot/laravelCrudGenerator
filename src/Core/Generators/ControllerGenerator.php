<?php

namespace Chatway\LaravelCrudGenerator\Core\Generators;

use Chatway\LaravelCrudGenerator\Core\Base\Interfaces\GeneratorInterface;
use Chatway\LaravelCrudGenerator\Core\DTO\ControllerParams;
use Chatway\LaravelCrudGenerator\Core\DTO\PropertyDTO;
use Chatway\LaravelCrudGenerator\Core\Entities\GeneratorForm;
use Chatway\LaravelCrudGenerator\Core\Helpers\ConsoleHelper;
use DB;
use File;
use Str;
use View;

class ControllerGenerator implements GeneratorInterface
{
    public string $baseClass = 'App\Http\Admin\Controllers\ResourceController';

    public function __construct(public GeneratorForm $generatorForm, public ControllerParams $controllerParams)
    {
    }

    public function generate()
    {
        $namespace = class_namespace($this->controllerParams->controllerName);
        $path = $this->generatorForm->mainPath . '/Core/Templates/Classes';
        View::addLocation($path);
        View::addNamespace($this->controllerParams->templateName, $path);
        $renderedModel = View::make($this->controllerParams->templateName)->with(
            [
                'controllerGenerator' => $this,
            ]);
        $filename = $this->generatorForm->resourceName . ($this->generatorForm::$CONTROLLER_SUFFIX) . ".php";
        $path = base_path(lcfirst($namespace));
        if (!File::isDirectory($path)) {
            File::makeDirectory($path, 0777, true, true);
        }

        if (!File::exists($path . '\\' . $filename) || $this->generatorForm->force) {
            File::delete($path . '\\' . $filename);
            if (File::put($path . '\\' . $filename, $renderedModel) !== false) {
                ConsoleHelper::info('Controller generated! Path in app: ' . lcfirst($namespace) . '\\' . $filename);
            } else {
                ConsoleHelper::error('Controller generate error!');
            }
        } else {
            ConsoleHelper::warning('Controller is exists! Add --force option to overwrite Controller!');
        }
    }


    public function getFormattedRule(PropertyDTO $property)
    {
        return "'{$property->name}' => '{$this->getRule($property)}',";
    }

    private function getRule(PropertyDTO $property)
    {
        $result = $property->nullable ? 'nullable|' : 'required|';
        $result .= str_contains($property->type, 'string')
            ? 'string|max:255'
            : (str_contains($property->type, 'Carbon') ? 'date'
                : 'numeric');
        if (str_contains($property->name, '_id')) {
            $tableName = str_replace('_id', '', $property->name);
            $tables = \Arr::pluck(DB::select('SHOW TABLES'), "Tables_in_" . config('database.connections.mysql.database'));
            if (($index = array_search($tableName, $tables)) !== false) {
                $result .= "|exists:{$tables[$index]},id";
            } elseif (($index = array_search(Str::singular($tableName), $tables)) !== false) {
                $result .= "|exists:{$tables[$index]},id";
            } elseif (($index = array_search(Str::plural($tableName), $tables)) !== false) {
                $result .= "|exists:{$tables[$index]},id";
            }
        }
        return $result;
    }
}
