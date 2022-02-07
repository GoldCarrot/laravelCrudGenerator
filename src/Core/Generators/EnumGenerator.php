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
        $this->filename = class_basename($this->enum->enumName) . ".php";
        $this->path = str_replace('\\', '/', base_path(lcfirst(class_namespace($this->generatorForm->enumName))));
    }

    public function generate()
    {
        View::addLocation($this->getPathTemplate());
        View::addNamespace('enum', $this->getPathTemplate());
        $renderedModel = View::make('enum')->with(
            [
                'generator' => $this,
            ]);
        if (!File::isDirectory($this->getPath())) {
            File::makeDirectory($this->getPath(), 0777, true, true);
        }

        if (!File::exists($this->getFilePath()) || $this->generatorForm->force) {
            File::delete($this->getFilePath());
            if (File::put($this->getFilePath(), $renderedModel) !== false) {
                ConsoleHelper::info("{$this->getFileName()} generated! Path in app: " . $this->getPath() . '/');
            } else {
                ConsoleHelper::error("{$this->getFileName()} generate error!");
            }
        } else {
            ConsoleHelper::warning("{$this->getFileName()} is exists! Add --force option to overwrite Enum!");
        }
    }
}
