<?php
/**
 * This is the template for generating the enum class of a specified table.
 */

/* @var $generator \Chatway\LaravelCrudGenerator\Core\Generators\RepositoryGenerator */
/* @var $generator->generatorForm->properties array list of properties (property => [type, name. comment]) */

echo "<?php\n";
?>

namespace {{ class_namespace($generator->generatorForm->enumName) }};


use {{ $generator->baseClass }};

/**
 * This is the enum class for table "{{ $generator->generatorForm->resourceTable }}".
 * Class {{ basename(($generator->generatorForm->enumName)) }}
 *
 * @package {{ class_namespace($generator->generatorForm->enumName) }}
*/

class {{ basename($generator->generatorForm->enumName) }} extends {{ basename($generator->baseClass) }}
{
@foreach($generator->enum->types as $type)
    public const {{strtoupper($type)}} = '{{$type}}';
@endforeach
@if(count($generator->enum->types) > 0)

    public static function keys(): array
    {
        return [
@foreach($generator->enum->types as $type)
            self::{{strtoupper($type)}},
@endforeach
        ];
    }
@endif
}
