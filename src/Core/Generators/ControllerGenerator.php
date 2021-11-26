<?php

namespace Chatway\LaravelCrudGenerator\Core\Generators;

use Chatway\LaravelCrudGenerator\Core\Base\Interfaces\GeneratorInterface;
use Chatway\LaravelCrudGenerator\Core\DTO\PropertyDTO;
use Chatway\LaravelCrudGenerator\Core\DTO\ResultGeneratorDTO;
use Chatway\LaravelCrudGenerator\Core\Entities\GeneratorForm;
use DB;
use File;
use Str;
use View;

class ControllerGenerator implements GeneratorInterface
{
    public $baseClassNs = 'App\Http\Admin\Controllers\\';
    public $baseClass   = 'ResourceController';

    public function __construct(public GeneratorForm $generatorForm)
    {
    }

    public function generate()
    {
        $namespace = $this->getNs();
        View::addLocation(app('path') . '/Console/Generator/Templates/Classes');
        View::addNamespace('controller', app('path') . '/Console/Generator/Templates/Classes');
        $renderedModel = View::make('controller')->with(
            [
                'controllerGenerator' => $this,
            ]);
        $filename = $this->generatorForm->resourceName . $this->generatorForm::CONTROLLER_SUFFIX . ".php";
        $path = base_path(lcfirst($namespace));
        if (!File::isDirectory($path)) {
            File::makeDirectory($path, 0777, true, true);
        }
        File::delete($path . '\\' . $filename);
        return new ResultGeneratorDTO(
            [
                'success'  => File::put($path . '\\' . $filename, $renderedModel),
                'fileName' => $path . $filename,
                'filePath' => lcfirst($namespace) . '\\' . $filename,
                'modelNs'  => $namespace,
            ]);
    }

    public function getFullName()
    {
        return $this->generatorForm->httpNs . $this->getControllerName();
    }

    public function getControllerName()
    {
        return $this->generatorForm->resourceName . 'Controller';
    }

    public function getNs()
    {
        return $this->generatorForm->httpNs;
    }

    public function getBaseClassWithNs()
    {
        return $this->baseClassNs . $this->baseClass;
    }

    public function getModelFullName()
    {
        return $this->generatorForm->baseNs . ModelGenerator::FOLDER_NAME . '\\' . $this->generatorForm->resourceName;
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
