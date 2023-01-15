<?php

namespace Chatway\LaravelCrudGenerator\Core\Scenarios;

use Chatway\LaravelCrudGenerator\Core\Base\BaseScenario;
use Chatway\LaravelCrudGenerator\Core\Base\Interfaces\ScenarioInterface;
use Chatway\LaravelCrudGenerator\Core\DTO\ControllerParams;
use Chatway\LaravelCrudGenerator\Core\DTO\RouteParams;
use Chatway\LaravelCrudGenerator\Core\DTO\ScenarioItem;
use Chatway\LaravelCrudGenerator\Core\Entities\GeneratorForm;
use Chatway\LaravelCrudGenerator\Core\Enums\ScenariosEnum;
use Chatway\LaravelCrudGenerator\Core\Generators\ControllerGenerator;
use Chatway\LaravelCrudGenerator\Core\Generators\EnumsGenerator;
use Chatway\LaravelCrudGenerator\Core\Generators\ModelGenerator;
use Chatway\LaravelCrudGenerator\Core\Generators\RepositoryGenerator;
use Chatway\LaravelCrudGenerator\Core\Generators\RouteGenerator;
use Chatway\LaravelCrudGenerator\Core\Generators\ServiceGenerator;
use Chatway\LaravelCrudGenerator\Core\Generators\ViewGenerator;
use Str;

class ModelScenario extends BaseScenario implements ScenarioInterface
{
    public string $name = ScenariosEnum::MODEL;

    public array $generators = [];

    public function __construct(private GeneratorForm $generatorForm)
    {
    }

    private function addItem($abstract, $options)
    {
        $this->generators[] = new ScenarioItem($abstract, $options);
    }

    public function init(): array
    {
        $this->addItem(ModelGenerator::class,
            [
                'modelName' => $this->generatorForm->baseNs . $this->generatorForm->folderNs . '\\'
                               . $this->generatorForm::$MODEL_FOLDER_NAME . '\\' . $this->generatorForm->resourceName,
            ]);
        return $this->generators;
    }
}