<?php

namespace Chatway\LaravelCrudGenerator\Core\Generators;

use Chatway\LaravelCrudGenerator\Core\Base\Interfaces\GeneratorInterface;
use Chatway\LaravelCrudGenerator\Core\Entities\GeneratorForm;
use Chatway\LaravelCrudGenerator\Core\Helpers\ConsoleHelper;
use File;
use View;

class RepositoryGenerator implements GeneratorInterface
{
    public string $baseClass     = 'App\Base\Repositories\BaseEloquentRepository';
    public string $baseInterface = 'App\Base\Interfaces\DataProviderInterface';
    public array $traits = ['App\Base\Traits\DataProviderTrait'];

    public function __construct(public GeneratorForm $generatorForm)
    {
    }

    public function generate()
    {
        $this->baseClass = GeneratorForm::getSafeEnv('GENERATOR_REPOSITORY_EXTENDS') ?? $this->baseClass;
        $this->baseInterface = GeneratorForm::getSafeEnv('GENERATOR_REPOSITORY_IMPLEMENTS') ?? $this->baseInterface;
        $namespace = class_namespace($this->generatorForm->repositoryName);
        $path = $this->generatorForm->mainPath . '/Core/Templates/Classes';
        View::addLocation($path);
        View::addNamespace('repository', $path);
        $renderedModel = View::make('repository')->with(
            [
                'generator' => $this,
            ]);
        $filename = "{$this->generatorForm->resourceName}Repository.php";
        $path = base_path(lcfirst($namespace));
        if (!File::isDirectory($path)) {
            File::makeDirectory($path, 0777, true, true);
        }

        if (!File::exists($path . '\\' . $filename) || $this->generatorForm->force) {
            File::delete($path . '\\' . $filename);
            if (File::put($path . '\\' . $filename, $renderedModel) !== false) {
                ConsoleHelper::info('Repository generated! Path in app: ' . lcfirst($namespace) . '\\' . $filename);
            } else {
                ConsoleHelper::error('Repository generate error!');
            }
        } else {
            ConsoleHelper::warning('Repository is exists! Add --force option to overwrite Repository!');
        }
    }
}
