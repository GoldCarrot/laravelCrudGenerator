<?php

namespace Chatway\LaravelCrudGenerator\Core\Generators;

use Chatway\LaravelCrudGenerator\Core\Base\Interfaces\GeneratorInterface;
use Chatway\LaravelCrudGenerator\Core\DTO\ResultGeneratorDTO;
use Chatway\LaravelCrudGenerator\Core\Entities\GeneratorForm;
use Chatway\LaravelCrudGenerator\Core\Helpers\ConsoleHelper;
use Chatway\LaravelCrudGenerator\GeneratorCommand;
use File;
use View;

class ModelGenerator implements GeneratorInterface
{
    public $baseClass = 'App\Base\Models\BaseModel';

    public function __construct(public GeneratorForm $generatorForm)
    {
    }

    public function generate()
    {
        $namespace = $this->generatorForm->getNsByClassName($this->generatorForm->modelName);
        $path = GeneratorCommand::$MAIN_PATH . '/Core/Templates/Classes';
        View::addLocation($path);
        View::addNamespace('model', $path);
        $renderedModel = View::make('model')->with(
            [
                'modelGenerator' => $this,
            ]);
        //dd($this->baseClass,$renderedModel->render(), $this->generatorForm->baseClass);
        $filename = "{$this->generatorForm->resourceName}.php";
        $path = base_path(lcfirst($namespace));
        if (!File::isDirectory($path)) {
            File::makeDirectory($path, 0777, true, true);
        }
        if (!File::exists($path . '\\' . $filename) || $this->generatorForm->force) {
            File::delete($path . '\\' . $filename);
            if (File::put($path . '\\' . $filename, $renderedModel) !== false) {
                ConsoleHelper::info('Model generated! Path in app: ' . lcfirst($namespace) . '\\' . $filename);
            } else {
                ConsoleHelper::error('Model generate error!');
            }
        } else {
            ConsoleHelper::info('Model is exists! Add --force option to overwrite Model!');
        }
    }
}
