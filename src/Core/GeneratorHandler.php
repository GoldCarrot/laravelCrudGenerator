<?php

namespace Chatway\LaravelCrudGenerator\Core;

use Chatway\LaravelCrudGenerator\Core\DTO\MainParams;
use Chatway\LaravelCrudGenerator\Core\Entities\GeneratorForm;
use Chatway\LaravelCrudGenerator\Core\Generators\ControllerGenerator;
use Chatway\LaravelCrudGenerator\Core\Generators\EnumGenerator;
use Chatway\LaravelCrudGenerator\Core\Generators\ModelGenerator;
use Chatway\LaravelCrudGenerator\Core\Generators\RepositoryGenerator;
use Chatway\LaravelCrudGenerator\Core\Generators\ServiceGenerator;
use Chatway\LaravelCrudGenerator\Core\Generators\ViewGenerator;
use Chatway\LaravelCrudGenerator\Core\Helpers\ConsoleHelper;
use Chatway\LaravelCrudGenerator\Core\Helpers\DB\ForeignKeyService;
use Illuminate\Console\Command;
use Illuminate\Console\Concerns\InteractsWithIO;

class GeneratorHandler
{
    //use InteractsWithIO;

    //public function __construct()
    //{
    //    parent::__construct();
    //}

    private function info($string)
    {
        $style = 'error';
        //echo "<$style>$string</$style>";
        ConsoleHelper::log($string, 'blue', true, 'black');
    }

    public function start(MainParams $mainParams)
    {
        ConsoleHelper::primary('------------Generator start!------------');
        $generatorForm = new GeneratorForm($mainParams, new ForeignKeyService());
        if ($generatorForm::TEST_MODE) {
            $this->error('Test mode: ' . $generatorForm->baseNs);
        }

        $result = (new ModelGenerator($generatorForm))->generate();
        if ($result->success !== false) {
            ConsoleHelper::info('Model generated! Path in app: ' . $result->filePath);
        }
        $result = (new ControllerGenerator($generatorForm))->generate();
        if ($result->success !== false) {
            ConsoleHelper::info('Controller generated! Path in app: ' . $result->filePath);
        }
        $result = (new RepositoryGenerator($generatorForm))->generate();
        if ($result->success !== false) {
            ConsoleHelper::info('Repository generated! Path in app: ' . $result->filePath);
        }
        $result = (new ServiceGenerator($generatorForm))->generate();
        if ($result->success !== false) {
            ConsoleHelper::info('Service generated! Path in app: ' . $result->filePath);
        }
        foreach ($generatorForm->enums as $enum) {
            $enum->enumName = $generatorForm->baseNs . $generatorForm::ENUM_FOLDER_NAME . '\\' . $generatorForm->resourceName
                              . ucfirst($enum->name);
            $result = (new EnumGenerator($generatorForm, $enum))->generate();
            if ($result->success !== false) {
                ConsoleHelper::info('Enum ' . $generatorForm->resourceName . ucfirst($enum->name) . ' generated! Path in app: '
                            . $result->filePath);
            }
        }

        $viewList = ['create', 'form', 'index', 'show', 'update'];
        foreach ($viewList as $item) {
            $result = (new ViewGenerator($generatorForm, ['viewName' => $item]))->generate();
            if ($result->success !== false) {
                ConsoleHelper::info("View $item generated! Path in app: " . $result->filePath);
            }
        }
        ConsoleHelper::primary('------------Generator finish!------------');
        ConsoleHelper::bell();
    }
}
