<?php

namespace Chatway\LaravelCrudGenerator\Core\Generators;

use Chatway\LaravelCrudGenerator\Core\Base\Generators\BaseEloquentGenerator;
use Chatway\LaravelCrudGenerator\Core\Base\Interfaces\GeneratorInterface;
use Chatway\LaravelCrudGenerator\Core\Entities\GeneratorForm;
use Chatway\LaravelCrudGenerator\Core\Helpers\ConsoleHelper;
use File;
use View;

class RepositoryGenerator extends BaseEloquentGenerator implements GeneratorInterface
{
    public string $baseClass     = 'App\Base\Repositories\BaseEloquentRepository';
    public string $baseInterface = 'App\Base\Interfaces\DataProviderInterface';
    public array  $traits        = ['App\Base\Traits\DataProviderTrait'];

    public function __construct(public GeneratorForm $generatorForm)
    {
        $this->pathTemplate = $this->generatorForm->mainPath . '/Core/Templates/Classes';
        $this->filename = "{$this->generatorForm->resourceName}Repository.php";
        $this->path = str_replace('\\', '/', base_path(lcfirst(class_namespace($this->generatorForm->repositoryName))));
        if (env('GENERATOR_REPOSITORY_TRAITS')) {
            $this->traits = [];
            foreach (explode(',', env('GENERATOR_REPOSITORY_TRAITS')) as $trait) {
                $this->traits[] = $trait;
            }
        }
    }

    public function generate()
    {
        $this->baseClass = env('GENERATOR_REPOSITORY_EXTENDS') ?? $this->baseClass;
        $this->baseInterface = env('GENERATOR_REPOSITORY_IMPLEMENTS') ?? $this->baseInterface;
        $templateName = $this->getTemplateFileName('classes', self::label());
        try {
            $renderedModel = View::make($templateName)->with(
                [
                    'generator' => $this,
                ]);
            if (!File::isDirectory($this->getPath())) {
                File::makeDirectory($this->getPath(), 0777, true, true);
            }
            if (($renderedModel && !File::exists($this->getFilePath())) || $this->generatorForm->force) {
                File::delete($this->getFilePath());
                if (File::put($this->getFilePath(), $renderedModel) !== false) {
                    ConsoleHelper::info("{$this->getFileName()} generated! Path in app: " . $this->getPath() . '/');
                } else {
                    ConsoleHelper::error("{$this->getFileName()} generate error!");
                }
            } else {
                ConsoleHelper::warning("{$this->getFileName()} is exists! Add --force option to overwrite Repository!");
            }
        } catch (\Throwable $e) {
            ConsoleHelper::error("{$this->getFileName()} generate error!");
            dd($e);
        }
    }
}
