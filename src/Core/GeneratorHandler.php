<?php

namespace Chatway\LaravelCrudGenerator\Core;

use App;
use Chatway\LaravelCrudGenerator\Core\Base\Generators\BaseEloquentGenerator;
use Chatway\LaravelCrudGenerator\Core\Base\Interfaces\GeneratorInterface;
use Chatway\LaravelCrudGenerator\Core\DTO\MainParams;
use Chatway\LaravelCrudGenerator\Core\Entities\GeneratorForm;
use Chatway\LaravelCrudGenerator\Core\Generators\ControllersGenerator;
use Chatway\LaravelCrudGenerator\Core\Generators\EnumsGenerator;
use Chatway\LaravelCrudGenerator\Core\Generators\ModelGenerator;
use Chatway\LaravelCrudGenerator\Core\Generators\PresenterGenerator;
use Chatway\LaravelCrudGenerator\Core\Generators\RepositoryGenerator;
use Chatway\LaravelCrudGenerator\Core\Generators\RoutesGenerator;
use Chatway\LaravelCrudGenerator\Core\Generators\ServiceGenerator;
use Chatway\LaravelCrudGenerator\Core\Generators\ViewsGenerator;
use Chatway\LaravelCrudGenerator\Core\Helpers\ConsoleHelper;
use Chatway\LaravelCrudGenerator\Core\Helpers\DB\ForeignKeyService;
use Illuminate\Contracts\Container\BindingResolutionException;

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

        /** @var BaseEloquentGenerator[] | array $generators */
        $generators = [
            ModelGenerator::class,
            ControllersGenerator::class,
            RepositoryGenerator::class,
            ServiceGenerator::class,
            PresenterGenerator::class,
            EnumsGenerator::class,
            ViewsGenerator::class,
            RoutesGenerator::class,
        ];
        if (!$mainParams->previewPaths) {
            foreach ($generators as $generator) {
                if (count($generatorForm->generateList) == 0 || in_array($generator::label(), $generatorForm->generateList)) {
                    try {
                        /** @var GeneratorInterface $generatorContainer */
                        $generatorContainer = App::makeWith($generator, ['generatorForm' => $generatorForm]);
                        switch ($generatorForm->action) {
                            case 'rollback':
                                $generatorContainer->rollback();
                                break;
                            default:
                                $generatorContainer->generate();
                        }
                    } catch (BindingResolutionException $e) {
                        ConsoleHelper::error($e->getMessage());
                    }
                }
            }
        }

        //dd(basename($generators[0]), $generators[0]::label());
        //if (!$mainParams->previewPaths) {
        //    if (count($generatorForm->generateList) == 0 || in_array('model', $generatorForm->generateList)) {
        //        (new ModelGenerator($generatorForm))->generate();
        //    }
        //    if (count($generatorForm->generateList) == 0 || in_array('controller', $generatorForm->generateList)) {
        //        (new ControllersGenerator($generatorForm))->generate();
        //    }
        //    if (count($generatorForm->generateList) == 0 || in_array('repository', $generatorForm->generateList)) {
        //        (new RepositoryGenerator($generatorForm))->generate();
        //    }
        //    if (count($generatorForm->generateList) == 0 || in_array('service', $generatorForm->generateList)) {
        //        (new ServiceGenerator($generatorForm))->generate();
        //    }
        //    if (count($generatorForm->generateList) == 0 || in_array('presenter', $generatorForm->generateList)) {
        //        (new PresenterGenerator($generatorForm))->generate();
        //    }
        //    if (count($generatorForm->generateList) == 0 || in_array('enum', $generatorForm->generateList)) {
        //        (new EnumsGenerator($generatorForm))->generate();
        //    }
        //    if (count($generatorForm->generateList) == 0 || in_array('view', $generatorForm->generateList)) {
        //        (new ViewsGenerator($generatorForm))->generate();
        //    }
        //    if (count($generatorForm->generateList) == 0 || in_array('route', $generatorForm->generateList)) {
        //        (new RoutesGenerator($generatorForm))->generate();
        //    }
        //}

        if ($mainParams->previewPaths) {
            ConsoleHelper::primary('------------Generator previewPaths finish!------------');
        } else {
            ConsoleHelper::primary('------------Generator finish!------------');
        }
        ConsoleHelper::bell();
    }
}
