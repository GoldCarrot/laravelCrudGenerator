<?php

namespace Chatway\LaravelCrudGenerator\Core\Generators;

use Chatway\LaravelCrudGenerator\Core\Base\Generators\BaseEloquentGenerator;
use Chatway\LaravelCrudGenerator\Core\Base\Interfaces\GeneratorInterface;
use Chatway\LaravelCrudGenerator\Core\Entities\GeneratorForm;
use Chatway\LaravelCrudGenerator\Core\Helpers\ConsoleHelper;
use File;
use View;

class ServiceGenerator extends BaseEloquentGenerator implements GeneratorInterface
{
    public string $baseClass = 'App\Base\Services\BaseService';
    public string $baseInterface = 'App\Base\Interfaces\ManageServiceInterface';

    public function __construct(public GeneratorForm $generatorForm)
    {
        $this->pathTemplate = $this->generatorForm->mainPath . '/Core/Templates/Classes';
        $this->filename = $this->generatorForm->resourceName . $this->generatorForm::$SERVICE_SUFFIX . ".php";
        $this->path = str_replace('\\', '/', base_path(lcfirst(class_namespace($this->generatorForm->serviceName))));
    }

    public function generate()
    {
        $this->baseClass = env('GENERATOR_SERVICE_EXTENDS') ?? $this->baseClass;
        $this->baseInterface = env('GENERATOR_SERVICE_IMPLEMENTS') ?? $this->baseInterface;
        $templateName = $this->getTemplateFileName('classes', self::label());
        $renderedModel = View::make($templateName)->with(
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
            ConsoleHelper::warning("{$this->getFileName()} is exists! Add --force option to overwrite Service!");
        }
    }
}
