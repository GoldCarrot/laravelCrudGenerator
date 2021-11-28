<?php

namespace Chatway\LaravelCrudGenerator\Commands;

use Chatway\LaravelCrudGenerator\Core\Entities\GeneratorForm;
use Chatway\LaravelCrudGenerator\Core\Generators\ControllerGenerator;
use Chatway\LaravelCrudGenerator\Core\Generators\EnumGenerator;
use Chatway\LaravelCrudGenerator\Core\Generators\ModelGenerator;
use Chatway\LaravelCrudGenerator\Core\Generators\RepositoryGenerator;
use Chatway\LaravelCrudGenerator\Core\Generators\ServiceGenerator;
use Chatway\LaravelCrudGenerator\Core\Generators\ViewGenerator;
use Chatway\LaravelCrudGenerator\Core\Helpers\DB\ForeignKeyService;
use DB;

class GeneratorTestCommand extends GeneratorCommand
{
}
