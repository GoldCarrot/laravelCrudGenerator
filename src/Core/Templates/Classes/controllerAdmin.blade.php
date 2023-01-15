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
use {{ $generator->scenarioValue('modelName') }};
use {{$generator->scenarioValue('repositoryName')}};
use {{$generator->scenarioValue('serviceName')}};
use Illuminate\Http\Request;

/**
* This is the controller class for table "{{ $generator->generatorForm->resourceTable }}".
* Class {{ \class_namespace($generator->controllerParams->controllerName) }}
*
* @package {{ $generator->controllerParams->controllerName }}
*/

class {{ class_basename($generator->controllerParams->controllerName) }} extends {{ class_basename($generator->controllerParams->baseClass) }}
{
public function __construct(
{{class_basename($generator->scenarioValue('repositoryName'))}} $repository,
{{class_basename($generator->scenarioValue('serviceName'))}} $service,
@foreach($generator->generatorForm->properties as $property)
    @if ($property->foreignKeyExists && !in_array($property->name, ['file_id', 'image_id']))
        @php
            $repository =
                    str_replace($generator->generatorForm::$MODEL_FOLDER_NAME, $generator->generatorForm::$REPOSITORY_FOLDER_NAME, $property->class);
            $repository .= $generator->generatorForm::$REPOSITORY_SUFFIX;
        @endphp
        private readonly \{!!  $repository  !!} ${{ lcfirst(class_basename($repository))  }},
    @endif
@endforeach
    )
{
parent::__construct($repository, $service);
}

protected function rules(Request $request, $model = null): array
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
@if(count($generator->generatorForm->enums) > 0)

    protected function viewParameters(): array
    {
    return [
    @foreach($generator->generatorForm->enums as $enum)
        '{{ Str::plural($enum->name) }}' => \{{ $enum->enumName }}::labels(),
    @endforeach
    @foreach($generator->generatorForm->properties as $property)
        @if ($property->foreignKeyExists && !in_array($property->name, ['file_id', 'image_id']))
            '{!!  Str::pluralStudly( lcfirst(class_basename($property->class)));  !!}' => $this->{{ lcfirst(class_basename($property->class)) . $generator->generatorForm::$REPOSITORY_SUFFIX }}->allActive(),
        @endif
    @endforeach
    ];
    }
@endif
}
