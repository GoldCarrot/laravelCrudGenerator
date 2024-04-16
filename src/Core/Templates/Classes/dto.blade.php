<?php
/**
 * This is the template for generating the service class of a specified table.
 */

/* @var \Chatway\LaravelCrudGenerator\Core\Generators\DtoGenerator $generator  */
/* @var $generator->generatorForm->properties array list of properties (property => [type, name. comment]) */

echo "<?php\n";
?>

namespace {{ class_namespace($generator->scenarioValue('dtoName')) }};

@if ($generator->baseClass)
use {{ $generator->baseClass }};
@endif
use {{ $generator->scenarioValue('modelName') }};
use Spatie\LaravelData\Optional;

class {{ class_basename($generator->scenarioValue('dtoName')) }}{{ $generator->baseClass ? (' extends ' . class_basename($generator->baseClass)) : '' }}
{
    public function __construct(
@foreach($generator->generatorForm->properties as $property)
@if(in_array($property->name, ['created_at', 'updated_at', 'deleted_at', 'id'])) @continue @endif
        public{{ $generator->versionPhpCompare() ? ' readonly' : '' }} Optional|{{ Str::camel($generator->generatorForm->getFormattedProperty($property->type, $property->name, $property->nullable)) }},
@endforeach()
    )
    {
    }
}
