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

class GeneratorHandler
{
    public function start(MainParams $mainParams)
    {
        if ($mainParams->previewPaths) {
            ConsoleHelper::primary('------------Generator previewPaths start!------------');
        } else {
            ConsoleHelper::primary('------------Generator start!------------');
        }
        $generatorForm = new GeneratorForm($mainParams, new ForeignKeyService());
        if ($generatorForm->testMode) {
            ConsoleHelper::error('Test mode: ' . $generatorForm->baseNs . '. Files generate to Test folder. Example selected namespace\\Test\\');
        }
        if (!$mainParams->previewPaths) {
            (new ModelGenerator($generatorForm))->generate();
            (new ControllerGenerator($generatorForm))->generate();
            (new RepositoryGenerator($generatorForm))->generate();
            (new ServiceGenerator($generatorForm))->generate();

            foreach ($generatorForm->enums as $enum) {
                (new EnumGenerator($generatorForm, $enum))->generate();
            }

            $viewList = ['create', 'form', 'index', 'show', 'update'];
            foreach ($viewList as $item) {
                (new ViewGenerator($generatorForm, ['viewName' => $item]))->generate();
            }
        }

        if ($mainParams->previewPaths) {
            ConsoleHelper::primary('------------Generator previewPaths finish!------------');
        } else {
            ConsoleHelper::primary('------------Generator finish!------------');
        }
        ConsoleHelper::bell();
    }
}
