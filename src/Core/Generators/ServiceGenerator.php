<?php

namespace Chatway\LaravelCrudGenerator\Core\Generators;

use Chatway\LaravelCrudGenerator\Core\Base\Interfaces\GeneratorInterface;
use Chatway\LaravelCrudGenerator\Core\DTO\ResultGeneratorDTO;
use Chatway\LaravelCrudGenerator\Core\Entities\GeneratorForm;
use File;
use View;

class ServiceGenerator implements GeneratorInterface
{
    public $baseClass = 'App\Base\Services\BaseService';
    public $baseInterface = 'App\Base\Interfaces\ManageServiceInterface';

    public function __construct(public GeneratorForm $generatorForm)
    {
    }

    public function generate()
    {
        $namespace = $this->generatorForm->getServiceNs();
        View::addLocation(app('path') . '/Console/Generator/Templates/Classes');
        View::addNamespace('service', app('path') . '/Console/Generator/Templates/Classes');
        $renderedModel = View::make('service')->with(
            [
                'serviceGenerator' => $this,
            ]);
        $filename = $this->generatorForm->resourceName . $this->generatorForm::SERVICE_SUFFIX . ".php";
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

    public function getBaseClassWithNs()
    {
        return $this->baseClassNs . $this->baseClass;
    }

    public function getBaseInterfaceWithNs()
    {
        return $this->baseInterfaceNs . '\\' . $this->baseInterface;
    }

    public function getFormattedProperty($property)
    {
        return "\$model->{$property->name} = Arr::get(\$data, '{$property->name}', \$model->{$property->name});";
    }
}
