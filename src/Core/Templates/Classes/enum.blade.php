<?php
/**
 * This is the template for generating the enum class of a specified table.
 */

/* @var $enumGenerator->generatorForm \Chatway\LaravelCrudGenerator\Core\Entities\GeneratorForm */
/* @var $enumGenerator \Chatway\LaravelCrudGenerator\Core\Generators\RepositoryGenerator */
/* @var $enumGenerator->generatorForm->properties array list of properties (property => [type, name. comment]) */

echo "<?php\n";
?>

namespace {{ class_namespace($enumGenerator->generatorForm->enumName) }};


use {{ $enumGenerator->baseClass }};

/**
 * This is the enum class for table "{{ $enumGenerator->generatorForm->resourceTable }}".
 * Class {{ basename(($enumGenerator->generatorForm->enumName)) }}
 *
 * @package {{ class_namespace($enumGenerator->generatorForm->enumName) }}
*/

class {{ basename($enumGenerator->generatorForm->enumName) }} extends {{ basename($enumGenerator->baseClass) }}
{
@foreach($enumGenerator->enum->types as $type)
    public const {{strtoupper($type)}} = '{{$type}}';
@endforeach
@if(count($enumGenerator->enum->types) > 0)

    public static function keys(): array
    {
        return [
@foreach($enumGenerator->enum->types as $type)
            self::{{strtoupper($type)}},
@endforeach
        ];
    }
@endif
}
