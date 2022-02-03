<?php

namespace Chatway\LaravelCrudGenerator\Core\Generators;

use Chatway\LaravelCrudGenerator\Core\Base\Generators\BaseEloquentGenerator;
use Chatway\LaravelCrudGenerator\Core\Base\Interfaces\GeneratorInterface;
use Chatway\LaravelCrudGenerator\Core\DTO\ControllerParams;
use Chatway\LaravelCrudGenerator\Core\DTO\PropertyDTO;
use Chatway\LaravelCrudGenerator\Core\Entities\GeneratorForm;
use Chatway\LaravelCrudGenerator\Core\Helpers\ConsoleHelper;
use DB;
use File;
use Str;
use View;

class ControllerGenerator extends BaseEloquentGenerator implements GeneratorInterface
{
    public string $baseClass = 'App\Http\Admin\Controllers\ResourceController';

    public function __construct(public GeneratorForm $generatorForm, public ControllerParams $controllerParams)
    {
        $this->pathTemplate = $this->generatorForm->mainPath . '/Core/Templates/Classes';
        $this->filename = $this->generatorForm->resourceName . ($this->generatorForm::$CONTROLLER_SUFFIX) . ".php";
        $this->path = base_path(lcfirst(class_namespace($this->controllerParams->controllerName)));
    }

    public function generate()
    {
        View::addLocation($this->pathTemplate);
        View::addNamespace($this->controllerParams->templateName, $this->pathTemplate);

        $renderedModel = View::make($this->controllerParams->templateName)->with(
            [
                'generator' => $this,
            ]);

        if (!File::isDirectory($this->path)) {
            File::makeDirectory($this->path, 0777, true, true);
        }

        if (!File::exists($this->path . '\\' . $this->filename) || $this->generatorForm->force) {
            File::delete($this->path . '\\' . $this->filename);
            if (File::put($this->path . '\\' . $this->filename, $renderedModel) !== false) {
                ConsoleHelper::info("$this->filename generated! Path in app: " . $this->path . '\\');
            } else {
                ConsoleHelper::error("$this->filename generate error!");
            }
        } else {
            ConsoleHelper::warning("$this->filename is exists! Add --force option to overwrite Controller!");
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
