<?php

namespace Chatway\LaravelCrudGenerator\Core\Generators;

use Arr;
use Chatway\LaravelCrudGenerator\Core\Base\Generators\BaseEloquentGenerator;
use Chatway\LaravelCrudGenerator\Core\Base\Interfaces\GeneratorInterface;
use Chatway\LaravelCrudGenerator\Core\DTO\PropertyDTO;
use Chatway\LaravelCrudGenerator\Core\Entities\GeneratorForm;
use Chatway\LaravelCrudGenerator\Core\Helpers\ClassHelper;
use Chatway\LaravelCrudGenerator\Core\Helpers\ConsoleHelper;
use File;
use Str;
use View;

class ResourceGenerator extends BaseEloquentGenerator implements GeneratorInterface
{
    public string $baseClass = 'App\Http\Admin\Controllers\ResourceController';

    public function __construct(public GeneratorForm $generatorForm, $options)
    {
        $this->pathTemplate = $this->generatorForm->mainPath . '/Core/Templates/Classes';
        $this->filename = class_basename(Arr::get($options, 'resourceClassName')) . ".php";
        $this->path = str_replace('\\', '/', base_path(lcfirst(class_namespace(Arr::get($options, 'resourceClassName')))));
    }

    public function generate()
    {
        $templateName = $this->getTemplateFileName('classes', self::label());

        if (!File::isDirectory($this->getPath())) {
            File::makeDirectory($this->getPath(), 0777, true, true);
        }

        if (!File::exists($this->getFilePath()) || $this->generatorForm->force) {
            $renderedModel = View::make($templateName)->with(
                [
                    'generator' => $this,
                ]);
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


    public function getFormattedRule(PropertyDTO $property): string
    {
        $propertyName = $property->name;
        if ($property->foreignKeyExists) {
            $propertyName = str_replace('_id', '', $property->name);
        }
        $field = Str::camel($propertyName);
        return "'{$field}' => {$this->getRule($property)},";
    }
    public function getChildren($externalForeignKey): string
    {
        $className = $externalForeignKey['className'];
        $field = Str::pluralStudly(lcfirst(class_basename($className)));
        $modelName = class_basename($className) . 'Resource';
        $value = "$modelName::collection(\$this->resource->$field)";
        return "'{$field}' => {$value},";
    }

    public function getUse(PropertyDTO $property)
    {
        if (str_contains($property->name, '_id') && $property->foreignKeyExists) {
            $tableName = str_replace('_id', '', $property->name);
            $modelName = ucfirst($tableName) . 'Resource';
            $resource = ClassHelper::getResourceByName($modelName);
            return str_contains($resource, '\\') ? $resource : null;
        }
        return null;
    }

    public function getUseChildren($externalForeignKey)
    {
        $className = $externalForeignKey['className'] . 'Resource';
        $resource = ClassHelper::getResourceByName(class_basename($className));
        return str_contains($resource, '\\') ? $resource : null;
    }

    private function getRule(PropertyDTO $property): string
    {
        if ($property->classTable == 'images' || $property->classTable == 'files') {
            $parameter = Str::singular($property->classTable);
            return "[\n                'other' => \$this->resource->{$parameter}?->widen(1000)->url,\n                'webp' => \$this->resource->{$parameter}?->widen(1000)->encode('webp')->url,\n            ]";
        }
        if (str_contains($property->name, '_id') && $property->foreignKeyExists) {
            $tableName = $property->classTable ?? str_replace('_id', '', $property->name);
            $propertyName = Str::camel($tableName);
            $modelName = Str::singular($propertyName);
            $modelName = Str::ucfirst($modelName);
            $modelName = $modelName . 'Resource';
            return "\$this->resource->$propertyName ? {$modelName}::make(\$this->resource->$propertyName) : null";
        }
        if ($property->isEnum) {
            $modelName = class_basename($property->enum->enumName);
            return "{$modelName}::label(\$this->resource->$property->name)";
        }
        if ($property->type == 'Carbon') {
            return "\$this->resource->$property->name ? date('Y-m-d H:i', strtotime(\$this->resource->$property->name)) : null";
        }

        return "\$this->resource->$property->name";
    }
}
