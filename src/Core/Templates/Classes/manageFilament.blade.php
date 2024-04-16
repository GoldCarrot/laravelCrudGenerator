<?php
/**
 * This is the template for generating the service class of a specified table.
 */

/* @var \Chatway\LaravelCrudGenerator\Core\Generators\DtoGenerator $generator  */
/* @var $generator->generatorForm->properties array list of properties (property => [type, name. comment]) */

echo "<?php\n";
?>

namespace {{ class_namespace($generator->scenarioValue('manageFilamentName')) }};

@if ($generator->baseClass)
use {{ $generator->baseClass }};
@endif
use {{ $generator->scenarioValue('filamentResourceName') }};
use {{ $generator->scenarioValue('modelName') }};
use {{ $generator->scenarioValue('dtoName') }};
use {{ $generator->scenarioValue('serviceName') }};
use Spatie\LaravelData\Optional;

class {{ class_basename($generator->scenarioValue('manageFilamentName')) }}{{ $generator->baseClass ? (' extends ' . class_basename($generator->baseClass)) : '' }}
{
    protected static string $resource = {{ class_basename($generator->scenarioValue('filamentResourceName')) }}::class;
    public static function create(array $state): {{ $generator->generatorForm->resourceName }}
    {
    return app({{ class_basename($generator->scenarioValue('serviceName')) }}::class)->create({{ class_basename($generator->scenarioValue('dtoName')) }}::from([
@foreach($generator->generatorForm->properties as $property)
@if(in_array($property->name, ['created_at', 'updated_at', 'deleted_at', 'id'])) @continue @endif
        '{{ Str::camel($property->name) }}' => data_get($state, '{{ $property->name }}', Optional::create()),
@endforeach()
    ]));
    }
}
