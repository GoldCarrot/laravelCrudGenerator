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
use Chatway\LaravelCrudGenerator\Core\Generators\DtoGenerator;
use Chatway\LaravelCrudGenerator\Core\Generators\EnumsGenerator;
use Chatway\LaravelCrudGenerator\Core\Generators\FilamentResourceGenerator;
use Chatway\LaravelCrudGenerator\Core\Generators\ManageFilamentGenerator;
use Chatway\LaravelCrudGenerator\Core\Generators\ModelGenerator;
use Chatway\LaravelCrudGenerator\Core\Generators\RepositoryGenerator;
use Chatway\LaravelCrudGenerator\Core\Generators\ResourceGenerator;
use Chatway\LaravelCrudGenerator\Core\Generators\RouteGenerator;
use Chatway\LaravelCrudGenerator\Core\Generators\ServiceFilamentGenerator;
use Chatway\LaravelCrudGenerator\Core\Generators\ServiceGenerator;
use Chatway\LaravelCrudGenerator\Core\Generators\ViewGenerator;
use Str;

class FilamentScenario extends BaseScenario implements ScenarioInterface
{
    public string $name = ScenariosEnum::DEFAULT;

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
                'controllerName' => $this->generatorForm->httpApiNs . 'Controllers\\' . $this->generatorForm->folderNs . '\\'
                                    . $this->generatorForm->resourceName
                                    . $this->generatorForm::$CONTROLLER_SUFFIX,
                'templateName'   => 'controllerApi',
                'baseClass'      => env('GENERATOR_API_CONTROLLER_EXTENDS') ?? 'App\Http\Api\Controllers\Controller',
            ]),
        ]);
        $this->addItem(RepositoryGenerator::class, [
            'repositoryName' => $this->generatorForm->baseNs . $this->generatorForm->folderNs . '\\'
                                . $this->generatorForm::$REPOSITORY_FOLDER_NAME . '\\'
                                . $this->generatorForm->resourceName . $this->generatorForm::$REPOSITORY_SUFFIX,
        ]);
        $this->addItem(DtoGenerator::class, [
            'dtoName' => $this->generatorForm->baseNs . $this->generatorForm->folderNs . '\\'
                             . $this->generatorForm::$DTO_FOLDER_NAME . '\\' . $this->generatorForm->resourceName
                             . $this->generatorForm::$DTO_SUFFIX,
        ]);
        $this->addItem(ServiceFilamentGenerator::class, [
            'serviceName' => $this->generatorForm->baseNs . $this->generatorForm->folderNs . '\\'
                             . $this->generatorForm::$SERVICE_FOLDER_NAME . '\\' . $this->generatorForm->resourceName
                             . $this->generatorForm::$SERVICE_SUFFIX,
        ]);
        $this->addItem(ManageFilamentGenerator::class, [
            'manageFilamentName' => 'App\Http\Admin\Resources\Resources\\' .$this->generatorForm::$MANAGE_FILAMENT_PREFIX . $this->generatorForm->resourceName,
        ]);
        $this->addItem(FilamentResourceGenerator::class, [
            'filamentResourceName' => 'App\Http\Admin\Resources\\' .$this->generatorForm->resourceName . $this->generatorForm::$RESOURCE_FILAMENT_SUFFIX,
        ]);
        $this->addItem(ResourceGenerator::class, [
            'resourceClassName' => $this->generatorForm->httpApiNs . $this->generatorForm::$RESOURCE_FOLDER_NAME . '\\'
                                   . $this->generatorForm->folderNs . '\\'
                                   . $this->generatorForm->resourceName
                                   . $this->generatorForm::$RESOURCE_SUFFIX,
        ]);
        $this->addItem(EnumsGenerator::class, []);
        $this->addItem(RouteGenerator::class, [
            'routeParams' => new RouteParams([
                'template' => "<?php

Route::group(['prefix' => '{{resourceTable}}', 'namespace' => '{{folderNs}}'], function () {
    Route::get('{{{resourceNameNotPlural}}:slug}', '{{resourceName}}Controller@show');
    Route::get('', '{{resourceName}}Controller@index');
});",
                'path'     => 'api',
                'filename' => '{{resourceTable}}',
            ]),
        ]);
        return $this->generators;
    }
}
