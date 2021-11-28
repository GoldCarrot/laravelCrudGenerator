<?php

namespace Chatway\LaravelCrudGenerator\Core\Generators;

use Chatway\LaravelCrudGenerator\Core\Base\Interfaces\GeneratorInterface;
use Chatway\LaravelCrudGenerator\Core\Entities\GeneratorForm;
use Chatway\LaravelCrudGenerator\Core\Helpers\ConsoleHelper;
use File;
use View;

class RepositoryGenerator implements GeneratorInterface
{
    const FOLDER_NAME = 'Repositories';
    public $baseClass     = 'App\Base\Repositories\BaseEloquentRepository';
    public $baseInterface = 'App\Base\Interfaces\DataProviderInterface';
    public $traits = ['App\Base\Traits\DataProviderTrait'];

    public function __construct(public GeneratorForm $generatorForm)
    {
    }

    public function generate()
    {
        $namespace = $this->generatorForm->getRepositoryNs();
        $path = $this->generatorForm->mainPath . '/Core/Templates/Classes';
        View::addLocation($path);
        View::addNamespace('repository', $path);
        $renderedModel = View::make('repository')->with(
            [
                'repositoryGenerator' => $this,
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
            ConsoleHelper::info('Repository is exists! Add --force option to overwrite Repository!');
        }
    }

    public function getFullName()
    {
        return $this->generatorForm->baseNs . $this::FOLDER_NAME . '\\' . $this->generatorForm->resourceName;
    }

    public function getModelNs()
    {
        return $this->generatorForm->baseNs . $this::FOLDER_NAME;
    }

    public function getBaseClassWithNs()
    {
        return $this->baseClassNs . $this->baseClass;
    }
}
