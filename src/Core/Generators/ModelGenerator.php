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
        $this->path = str_replace('\\', '/', base_path(lcfirst(class_namespace($this->generatorForm->modelName))));
    }

    public function generate()
    {
        $this->baseClass = GeneratorForm::getSafeEnv('GENERATOR_MODEL_EXTENDS') ?? $this->baseClass;
        View::addLocation($this->getPathTemplate());
        View::addNamespace('model', $this->getPathTemplate());
        $renderedModel = View::make('model')->with(
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
            ConsoleHelper::warning("{$this->getFileName()} is exists! Add --force option to overwrite Model!");
        }
    }
}
