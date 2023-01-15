<?php

namespace Chatway\LaravelCrudGenerator\Core;

use Artisan;
use Chatway\LaravelCrudGenerator\Core\Base\Interfaces\GeneratorInterface;
use Chatway\LaravelCrudGenerator\Core\DTO\MainParams;
use Chatway\LaravelCrudGenerator\Core\Entities\GeneratorForm;
use Chatway\LaravelCrudGenerator\Core\Helpers\ConsoleHelper;
use Chatway\LaravelCrudGenerator\Core\Helpers\DB\ForeignKeyService;
use Throwable;

class GeneratorHandler
{
    public function start(MainParams $mainParams): void
    {
        ConsoleHelper::primary('------------Generator start!------------');
        $generatorForm = new GeneratorForm($mainParams, new ForeignKeyService());
        if ($generatorForm->testMode) {
            ConsoleHelper::error('Test mode: ' . $generatorForm->baseNs
                                 . '. Files generate to Test folder. Example selected namespace\\Test\\');
        }

        foreach ($generatorForm->generators as $generator) {
            //try {
                /** @var GeneratorInterface $generatorContainer */
                $generatorContainer = is_string($generator) ? app($generator) : app($generator->abstract, ['generatorForm' => $generatorForm, 'options' => $generator->options]);
                switch ($generatorForm->action) {
                    case 'rollback':
                        $generatorContainer->rollback();
                        break;
                    default:
                        $generatorContainer->generate();
                }
            //} catch (Throwable $e) {
            //    ConsoleHelper::error($e->getMessage());
            //}
        }

        ConsoleHelper::primary('------------Generator finish!------------');
        Artisan::call('view:clear');
        ConsoleHelper::bell();
    }
}
