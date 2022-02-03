<?php

namespace Chatway\LaravelCrudGenerator\Core\Generators;

use Chatway\LaravelCrudGenerator\Core\Base\Generators\BaseEloquentGenerator;
use Chatway\LaravelCrudGenerator\Core\Base\Interfaces\GeneratorInterface;
use Chatway\LaravelCrudGenerator\Core\DTO\EnumParams;
use Chatway\LaravelCrudGenerator\Core\Entities\GeneratorForm;
use Chatway\LaravelCrudGenerator\Core\Helpers\ConsoleHelper;
use File;
use View;

/**
 * @property EnumParams $enum
 */
class EnumGenerator extends BaseEloquentGenerator implements GeneratorInterface
{
    public string $baseClass = 'App\Base\Enums\StatusEnum';

    public function __construct(public GeneratorForm $generatorForm, public EnumParams $enum)
    {
        $this->generatorForm->enumName = $this->enum->enumName;
        $this->pathTemplate = $this->generatorForm->mainPath . '/Core/Templates/Classes';
        $this->filename = basename($this->enum->enumName) . ".php";
        $this->path = base_path(lcfirst(class_namespace($this->generatorForm->enumName)));
    }

    public function generate()
    {
        View::addLocation($this->pathTemplate);
        View::addNamespace('enum', $this->pathTemplate);
        $renderedModel = View::make('enum')->with(
            [
                'generator' => $this,
            ]);
        if (!File::isDirectory($this->path)) {
            File::makeDirectory($this->path, 0777, true, true);
        }

        if (!File::exists($this->path . '\\' . $this->filename) || $this->generatorForm->force) {
            File::delete($this->path . '\\' . $this->filename);
            if (File::put($this->path . '\\' . $this->filename, $renderedModel) !== false) {
                ConsoleHelper::info("$this->filename generated! Path in app: " . $this->path . '\\', 'asd');
            } else {
                ConsoleHelper::error("$this->filename generate error!");
            }
        } else {
            ConsoleHelper::warning("$this->filename is exists! Add --force option to overwrite Enum!");
        }
    }
}
