<?php

namespace Chatway\LaravelCrudGenerator\Core\Generators;

use Chatway\LaravelCrudGenerator\Core\Base\Interfaces\GeneratorInterface;
use Chatway\LaravelCrudGenerator\Core\Entities\GeneratorForm;
use Chatway\LaravelCrudGenerator\Core\Helpers\ConsoleHelper;
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
        $path = $this->generatorForm->mainPath . '/Core/Templates/Classes';
        View::addLocation($path);
        View::addNamespace('service', $path);
        $renderedModel = View::make('service')->with(
            [
                'serviceGenerator' => $this,
            ]);
        $filename = $this->generatorForm->resourceName . $this->generatorForm::SERVICE_SUFFIX . ".php";
        $path = base_path(lcfirst($namespace));
        if (!File::isDirectory($path)) {
            File::makeDirectory($path, 0777, true, true);
        }

        if (!File::exists($path . '\\' . $filename) || $this->generatorForm->force) {
            File::delete($path . '\\' . $filename);
            if (File::put($path . '\\' . $filename, $renderedModel) !== false) {
                ConsoleHelper::info('Service generated! Path in app: ' . lcfirst($namespace) . '\\' . $filename);
            } else {
                ConsoleHelper::error('Service generate error!');
            }
        } else {
            ConsoleHelper::info('Service is exists! Add --force option to overwrite Service!');
        }
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
