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

class AdminScenario extends BaseScenario implements ScenarioInterface
{
    public string $name = ScenariosEnum::ADMIN;

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
        $this->addItem(ControllerGenerator::class, [
            'controllerParams' => new ControllerParams([
                'controllerName' => $this->generatorForm->httpNs . 'Controllers\\' . $this->generatorForm->folderNs . '\\'
                                    . $this->generatorForm->resourceName
                                    . $this->generatorForm::$CONTROLLER_SUFFIX,
                'templateName'   => 'controllerAdmin',
                'baseClass'      => env('GENERATOR_ADMIN_CONTROLLER_EXTENDS') ??
                                    'App\Http\Admin\Controllers\ResourceController',
            ]),
        ]);
        $this->addItem(RepositoryGenerator::class, [
            'repositoryName' => $this->generatorForm->baseNs . $this->generatorForm->folderNs . '\\'
                                . $this->generatorForm::$REPOSITORY_FOLDER_NAME . '\\'
                                . $this->generatorForm->resourceName . $this->generatorForm::$REPOSITORY_SUFFIX,
        ]);
        $this->addItem(ServiceGenerator::class, [
            'serviceName' => $this->generatorForm->baseNs . $this->generatorForm->folderNs . '\\'
                             . $this->generatorForm::$SERVICE_FOLDER_NAME . '\\' . $this->generatorForm->resourceName
                             . $this->generatorForm::$SERVICE_SUFFIX,
        ]);
        $this->addItem(EnumsGenerator::class, []);
        $this->addItem(ViewGenerator::class, [
            'viewName'  => 'create',
            'viewsPath' => env('GENERATOR_VIEWS_PATH') ??
                           'views\admin\\' . Str::pluralStudly(lcfirst(class_basename($this->generatorForm->resourceName))),
        ]);
        $this->addItem(ViewGenerator::class, [
            'viewName'  => 'form',
            'viewsPath' => env('GENERATOR_VIEWS_PATH') ??
                           'views\admin\\' . Str::pluralStudly(lcfirst(class_basename($this->generatorForm->resourceName))),
        ]);
        $this->addItem(ViewGenerator::class, [
            'viewName'  => 'index',
            'viewsPath' => env('GENERATOR_VIEWS_PATH') ??
                           'views\admin\\' . Str::pluralStudly(lcfirst(class_basename($this->generatorForm->resourceName))),
        ]);
        $this->addItem(ViewGenerator::class, [
            'viewName'  => 'update',
            'viewsPath' => env('GENERATOR_VIEWS_PATH') ??
                           'views\admin\\' . Str::pluralStudly(lcfirst(class_basename($this->generatorForm->resourceName))),
        ]);
        $this->addItem(RouteGenerator::class, [
            'routeParams' => new RouteParams([
                'template' => "<?php

Route::group(['middleware' => ['admin.auth']], function () {
    Route::resources([
        '{{resourceNamePlural}}' => '{{folderNs}}\{{resourceName}}Controller',
    ]);
});",
                'path'     => 'admin',
                'filename' => 'resource{{resourceName}}',
            ]),
        ]);
        return $this->generators;
    }
}