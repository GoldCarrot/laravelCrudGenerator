<?php

namespace Chatway\LaravelCrudGenerator\Core\Generators;

use Chatway\LaravelCrudGenerator\Core\Base\Generators\BaseEloquentGenerator;
use Chatway\LaravelCrudGenerator\Core\Base\Interfaces\GeneratorInterface;
use Chatway\LaravelCrudGenerator\Core\Entities\GeneratorForm;
use Chatway\LaravelCrudGenerator\Core\Helpers\ConsoleHelper;
use File;
use View;

class ModelGenerator extends BaseEloquentGenerator implements GeneratorInterface
{
    public string $baseClass = 'App\Base\Models\BaseModel';

    public function __construct(public GeneratorForm $generatorForm)
    {
        $this->pathTemplate = $this->generatorForm->mainPath . '/Core/Templates/Classes';
        $this->filename = "{$this->generatorForm->resourceName}.php";
        $this->path = base_path(lcfirst(class_namespace($this->generatorForm->modelName)));
    }

    public function generate()
    {
        $this->baseClass = GeneratorForm::getSafeEnv('GENERATOR_MODEL_EXTENDS') ?? $this->baseClass;
        View::addLocation($this->pathTemplate);
        View::addNamespace('model', $this->pathTemplate);
        $renderedModel = View::make('model')->with(
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
            ConsoleHelper::warning("$this->filename is exists! Add --force option to overwrite Model!");
        }
    }
}
