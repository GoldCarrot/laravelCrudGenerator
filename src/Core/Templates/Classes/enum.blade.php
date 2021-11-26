<?php
/**
 * This is the template for generating the enum class of a specified table.
 */

/* @var $enumGenerator->generatorForm \Chatway\LaravelCrudGenerator\Core\Entities\GeneratorForm */
/* @var $enumGenerator \Chatway\LaravelCrudGenerator\Core\Generators\RepositoryGenerator */
/* @var $enumGenerator->generatorForm->properties array list of properties (property => [type, name. comment]) */

echo "<?php\n";
?>

namespace {{ $enumGenerator->generatorForm->getEnumNs() }};


use {{ $enumGenerator->getBaseClassWithNs() }};

/**
 * This is the enum class for table "{{ $enumGenerator->generatorForm->resourceTable }}".
 * Class {{ $enumGenerator->generatorForm->getEnumName() }}
 *
 * @package {{ $enumGenerator->generatorForm->getEnumNs() }}
*/

class {{ $enumGenerator->generatorForm->getEnumName() }} extends {{ $enumGenerator->baseClass }}
{
    public static function keys(): array
    {
        return [
        ];
    }
}
