<?php

namespace Chatway\LaravelCrudGenerator\Core\Generators;

use Chatway\LaravelCrudGenerator\Core\Base\Generators\BaseEloquentGenerator;
use Chatway\LaravelCrudGenerator\Core\Base\Interfaces\GeneratorInterface;
use Chatway\LaravelCrudGenerator\Core\Entities\GeneratorForm;
use Chatway\LaravelCrudGenerator\Core\Helpers\ConsoleHelper;
use File;
use View;

class FilamentResourceGenerator extends BaseEloquentGenerator implements GeneratorInterface
{
    public string $baseClass = 'AbstractResource';

    public function __construct(public GeneratorForm $generatorForm, $options)
    {
        $this->pathTemplate = $this->generatorForm->mainPath . '/Core/Templates/Classes';
        $this->filename = class_basename($this->scenarioValue('filamentResourceName')) . ".php";
        $this->path = str_replace('\\', '/', base_path(lcfirst(class_namespace(\Arr::get($options, 'filamentResourceName')))));
    }

    public function generate()
    {
        $this->baseClass = env('GENERATOR_MANAGE_FILAMENT_EXTENDS') ?? $this->baseClass;
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
            ConsoleHelper::warning("{$this->getFileName()} is exists! Add --force option to overwrite Service!");
        }
    }
}
