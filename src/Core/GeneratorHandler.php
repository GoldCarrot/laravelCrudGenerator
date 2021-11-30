<?php

namespace Chatway\LaravelCrudGenerator\Core;

use Chatway\LaravelCrudGenerator\Core\DTO\ControllerParams;
use Chatway\LaravelCrudGenerator\Core\DTO\MainParams;
use Chatway\LaravelCrudGenerator\Core\DTO\RouteParams;
use Chatway\LaravelCrudGenerator\Core\Entities\GeneratorForm;
use Chatway\LaravelCrudGenerator\Core\Generators\ControllerGenerator;
use Chatway\LaravelCrudGenerator\Core\Generators\EnumGenerator;
use Chatway\LaravelCrudGenerator\Core\Generators\ModelGenerator;
use Chatway\LaravelCrudGenerator\Core\Generators\PresenterGenerator;
use Chatway\LaravelCrudGenerator\Core\Generators\RepositoryGenerator;
use Chatway\LaravelCrudGenerator\Core\Generators\RouteGenerator;
use Chatway\LaravelCrudGenerator\Core\Generators\ServiceGenerator;
use Chatway\LaravelCrudGenerator\Core\Generators\ViewGenerator;
use Chatway\LaravelCrudGenerator\Core\Helpers\ConsoleHelper;
use Chatway\LaravelCrudGenerator\Core\Helpers\DB\ForeignKeyService;
use Chatway\LaravelCrudGenerator\Core\Templates\Routes\GeneratorRouteTemplates;

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
            ConsoleHelper::error('Test mode: ' . $generatorForm->baseNs
                                 . '. Files generate to Test folder. Example selected namespace\\Test\\');
        }

        if (!$mainParams->previewPaths) {
            (new ModelGenerator($generatorForm))->generate();

            foreach ($generatorForm->controllers as $controller) {
                (new ControllerGenerator($generatorForm, $controller))->generate();
            }

            (new RepositoryGenerator($generatorForm))->generate();
            (new ServiceGenerator($generatorForm))->generate();
            (new PresenterGenerator($generatorForm))->generate();
            foreach ($generatorForm->enums as $enum) {
                (new EnumGenerator($generatorForm, $enum))->generate();
            }

            $viewList = ['create', 'form', 'index', 'show', 'update'];
            foreach ($viewList as $item) {
                (new ViewGenerator($generatorForm, ['viewName' => $item]))->generate();
            }

            $routeTemplates = (new GeneratorRouteTemplates())->getRoutes();
            foreach ($routeTemplates as $routeTemplate) {
                (new RouteGenerator($generatorForm, new RouteParams($routeTemplate)))->generate();
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
