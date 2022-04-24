<?php

namespace Chatway\LaravelCrudGenerator\Core\Generators;

use Chatway\LaravelCrudGenerator\Core\Base\Generators\BaseEloquentGenerator;
use Chatway\LaravelCrudGenerator\Core\Base\Interfaces\GeneratorInterface;
use Chatway\LaravelCrudGenerator\Core\DTO\PropertyDTO;
use Chatway\LaravelCrudGenerator\Core\Entities\GeneratorForm;
use Chatway\LaravelCrudGenerator\Core\Helpers\ConsoleHelper;
use File;
use Str;
use View;

class ResourceGenerator extends BaseEloquentGenerator implements GeneratorInterface
{
    public string $baseClass = 'App\Http\Admin\Controllers\ResourceController';

    public function __construct(public GeneratorForm $generatorForm)
    {
        $this->pathTemplate = $this->generatorForm->mainPath . '/Core/Templates/Classes';
        $this->filename = class_basename($this->generatorForm->resourceClassName) . ".php";
        $this->path = str_replace('\\', '/', base_path(lcfirst(class_namespace($this->generatorForm->resourceClassName))));
        //dd($this->path);
    }

    public function generate()
    {
        View::addLocation($this->getPathTemplate());
        View::addNamespace('resource', $this->getPathTemplate());
        $renderedModel = View::make('resource')->with(
            [
                'generator' => $this,
            ]);
        if (!File::isDirectory($this->getPath())) {
            File::makeDirectory($this->getPath(), 0777, true, true);
        }

        if (!File::exists($this->getFilePath()) || $this->generatorForm->force) {
            File::delete($this->getFilePath());
            if (File::put($this->getFilePath(), $renderedModel) !== false) {
                ConsoleHelper::info("{$this->getFileName()} generated! Path in app: " . $this->getPath() . '/');
            } else {
                ConsoleHelper::error("{$this->getFileName()} generate error!");
            }
        } else {
            ConsoleHelper::warning("{$this->getFileName()} is exists! Add --force option to overwrite Resource!");
        }
    }


    public function getFormattedRule(PropertyDTO $property)
    {
        $field = \Str::camel(str_replace('_id', '', $property->name));
        return "'{$field}' => {$this->getRule($property)},";
    }

    private function getRule(PropertyDTO $property)
    {
        if ($property->classTable == 'images' || $property->classTable == 'files') {
            $parameter = Str::singular($property->classTable);
            return "\$this->resource->{$parameter}->url ?? null";
        }
        if (str_contains($property->name, '_id')) {
            $tableName = str_replace('_id', '', $property->name);
            $modelName = ($property->class ?? ucfirst($tableName)) . 'Presenter';
            //return json_encode($property);
            return "\$this->resource->$tableName ? (new \\{$modelName}(\$this->resource->$tableName))->toArray() : null";
        }
        if ($property->isEnum) {
            return "\\{$property->enum->enumName}::label(\$this->resource->$property->name)";
        }
        if ($property->type == 'Carbon') {
            return "\$this->resource->$property->name ? date('Y-m-d H:i', strtotime(\$this->resource->$property->name)) : null";
        }

        return "\$this->resource->$property->name";
    }
}
