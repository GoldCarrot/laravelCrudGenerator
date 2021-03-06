<?php
/**
 * This is the template for generating the controller class of a specified table.
 */

/* @var $generator \Chatway\LaravelCrudGenerator\Core\Generators\ControllerGenerator */
/* @var $generator->generatorForm->properties array list of properties (property => [type, name. comment]) */

echo "<?php\n";
?>

namespace {{ class_namespace($generator->controllerParams->controllerName) }};

use {{ $generator->controllerParams->baseClass }};
use {{ $generator->generatorForm->modelName }};

/**
 * This is the controller class for table "{{ $generator->generatorForm->resourceTable }}".
 * Class {{ \class_namespace($generator->controllerParams->controllerName) }}
 *
 * @package {{ $generator->controllerParams->controllerName }}
*/

class {{ class_basename($generator->controllerParams->controllerName) }} extends {{ class_basename($generator->controllerParams->baseClass) }}
{
    public function __construct(\{{$generator->generatorForm->repositoryName}} $repository, \{{$generator->generatorForm->serviceName}} $service)
    {
        parent::__construct($repository, $service);
    }


    protected function rules($model = null): array
    {
        return [
@foreach($generator->generatorForm->properties as $property)
@if($property->name != 'id' && !$property->inlet)
            {!!  $generator->getFormattedRule($property)  !!}
@endif
@endforeach
        ];
    }
    protected function resourceClass(): string
    {
        return {{ $generator->generatorForm->resourceName }}::class;
    }
}
