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
        $this->path = base_path(lcfirst(class_namespace($this->generatorForm->repositoryName)));
    }

    public function generate()
    {
        $this->baseClass = GeneratorForm::getSafeEnv('GENERATOR_REPOSITORY_EXTENDS') ?? $this->baseClass;
        $this->baseInterface = GeneratorForm::getSafeEnv('GENERATOR_REPOSITORY_IMPLEMENTS') ?? $this->baseInterface;
        View::addLocation($this->pathTemplate);
        View::addNamespace(self::label(), $this->pathTemplate);
        try {
            $renderedModel = View::make(self::label())->with(
                [
                    'generator' => $this,
                ]);
            if (!File::isDirectory($this->path)) {
                File::makeDirectory($this->path, 0777, true, true);
            }
            if (($renderedModel && !File::exists($this->path . '\\' . $this->filename)) || $this->generatorForm->force) {
                File::delete($this->path . '\\' . $this->filename);
                if (File::put($this->path . '\\' . $this->filename, $renderedModel) !== false) {
                    ConsoleHelper::info("$this->filename generated! Path in app: " . $this->path . '\\');
                } else {
                    ConsoleHelper::error("$this->filename generate error!");
                }
            } else {
                ConsoleHelper::warning("$this->filename is exists! Add --force option to overwrite Repository!");
            }
        } catch (\Throwable $e) {
            ConsoleHelper::error("$this->filename generate error!");
            dd($e);
        }
    }
}
