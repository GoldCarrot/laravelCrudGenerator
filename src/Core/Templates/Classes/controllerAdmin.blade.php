<?php
/**
 * This is the template for generating the controller class of a specified table.
 */

/* @var $controllerGenerator \Chatway\LaravelCrudGenerator\Core\Generators\ControllerGenerator */
/* @var $controllerGenerator->generatorForm->properties array list of properties (property => [type, name. comment]) */

echo "<?php\n";
?>

namespace {{ class_namespace($controllerGenerator->controllerParams->controllerName) }};

use {{ $controllerGenerator->controllerParams->baseClass }};
use {{ $controllerGenerator->generatorForm->modelName }};

/**
 * This is the controller class for table "{{ $controllerGenerator->generatorForm->resourceTable }}".
 * Class {{ \class_namespace($controllerGenerator->controllerParams->controllerName) }}
 *
 * @package {{ $controllerGenerator->controllerParams->controllerName }}
*/

class {{ basename($controllerGenerator->controllerParams->controllerName) }} extends {{ basename($controllerGenerator->controllerParams->baseClass) }}
{
    public function __construct(\{{$controllerGenerator->generatorForm->repositoryName}} $repository, \{{$controllerGenerator->generatorForm->serviceName}} $service)
    {
        parent::__construct($repository, $service);
    }


    protected function rules($model = null): array
    {
        return [
@foreach($controllerGenerator->generatorForm->properties as $property)
@if($property->name != 'id' && !$property->inlet)
            {!!  $controllerGenerator->getFormattedRule($property)  !!}
@endif
@endforeach
        ];
    }
    protected function resourceClass(): string
    {
        return {{ $controllerGenerator->generatorForm->resourceName }}::class;
    }
}
