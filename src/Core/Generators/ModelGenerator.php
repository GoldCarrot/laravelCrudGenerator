<?php

namespace Chatway\LaravelCrudGenerator\Core\Generators;

use Chatway\LaravelCrudGenerator\Core\Base\Interfaces\GeneratorInterface;
use Chatway\LaravelCrudGenerator\Core\DTO\ResultGeneratorDTO;
use Chatway\LaravelCrudGenerator\Core\Entities\GeneratorForm;
use Chatway\LaravelCrudGenerator\GeneratorCommand;
use File;
use View;

class ModelGenerator implements GeneratorInterface
{
    public $baseClass = 'App\Base\Models\BaseModel';

    public function __construct(public GeneratorForm $generatorForm)
    {
    }

    public function generate(): ResultGeneratorDTO
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
        File::delete($path . '\\' . $filename);
        return new ResultGeneratorDTO(
            [
                'success'  => File::put($path . '\\' . $filename, $renderedModel),
                'fileName' => $path . $filename,
                'filePath' => lcfirst($namespace) . '\\' . $filename,
                'modelNs'  => $namespace,
            ]);
    }
}
