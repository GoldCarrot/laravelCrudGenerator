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
            if (count($generatorForm->generateList) == 0 || in_array('model', $generatorForm->generateList)) {
                (new ModelGenerator($generatorForm))->generate();
            }
            if (count($generatorForm->generateList) == 0 || in_array('controller', $generatorForm->generateList)) {
                foreach ($generatorForm->controllers as $controller) {
                    (new ControllerGenerator($generatorForm, $controller))->generate();
                }
            }
            if (count($generatorForm->generateList) == 0 || in_array('repository', $generatorForm->generateList)) {
                (new RepositoryGenerator($generatorForm))->generate();
            }
            if (count($generatorForm->generateList) == 0 || in_array('service', $generatorForm->generateList)) {
                (new ServiceGenerator($generatorForm))->generate();
            }
            if (count($generatorForm->generateList) == 0 || in_array('presenter', $generatorForm->generateList)) {
                (new PresenterGenerator($generatorForm))->generate();
            }
            if (count($generatorForm->generateList) == 0 || in_array('enum', $generatorForm->generateList)) {
                foreach ($generatorForm->enums as $enum) {
                    (new EnumGenerator($generatorForm, $enum))->generate();
                }
            }
            if (count($generatorForm->generateList) == 0 || in_array('view', $generatorForm->generateList)) {
                $viewList = ['create', 'form', 'index', 'show', 'update'];
                foreach ($viewList as $item) {
                    (new ViewGenerator($generatorForm, ['viewName' => $item]))->generate();
                }
            }
            if (count($generatorForm->generateList) == 0 || in_array('route', $generatorForm->generateList)) {
                $routeTemplates = (new GeneratorRouteTemplates())->getRoutes();
                foreach ($routeTemplates as $routeTemplate) {
                    (new RouteGenerator($generatorForm, new RouteParams($routeTemplate)))->generate();
                }
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
