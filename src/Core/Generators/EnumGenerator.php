<?php

namespace Chatway\LaravelCrudGenerator\Core\Generators;

use Chatway\LaravelCrudGenerator\Core\Base\Interfaces\GeneratorInterface;
use Chatway\LaravelCrudGenerator\Core\DTO\EnumParams;
use Chatway\LaravelCrudGenerator\Core\DTO\ResultGeneratorDTO;
use Chatway\LaravelCrudGenerator\Core\Entities\GeneratorForm;
use Chatway\LaravelCrudGenerator\GeneratorCommand;
use File;
use Illuminate\Validation\Rules\Enum;
use View;

/**
 * @property EnumParams $enum
 */
class EnumGenerator implements GeneratorInterface
{
    public string $baseClass = 'App\Base\Enums\StatusEnum';

    public function __construct(public GeneratorForm $generatorForm, public EnumParams $enum)
    {
    }

    public function generate()
    {
        $this->generatorForm->enumName = $this->enum->enumName;
        $namespace = class_namespace($this->generatorForm->enumName);
        $pathTemplate = GeneratorCommand::$MAIN_PATH . '/Core/Templates/Classes';
        View::addLocation($pathTemplate);
        View::addNamespace('enum', $pathTemplate);
        $renderedModel = View::make('enum')->with(
            [
                'enumGenerator' => $this,
            ]);
        $filename = $this->generatorForm->resourceName . ucfirst($this->enum->name) . ".php";
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
        return $this->baseClassNs . '\\' . $this->baseClass;
    }

    public function getFormattedProperty($property)
    {
        return "\$model->{$property['name']} = Arr::get(\$data, '{$property['name']}', \$model->{$property['name']});";
    }
}
