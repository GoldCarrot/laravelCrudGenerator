<?php
/**
 * This is the template for generating the repository class of a specified table.
 */

/* @var $repositoryGenerator->generatorForm \Chatway\LaravelCrudGenerator\Core\Entities\GeneratorForm */
/* @var $repositoryGenerator \Chatway\LaravelCrudGenerator\Core\Generators\RepositoryGenerator */
/* @var $repositoryGenerator->generatorForm->properties array list of properties (property => [type, name. comment]) */

echo "<?php\n";
?>

namespace {{ class_namespace($repositoryGenerator->generatorForm->repositoryName) }};


use {{ $repositoryGenerator->baseClass }};
use {{ $repositoryGenerator->generatorForm->modelName }};
@if ($repositoryGenerator->baseInterface)
use {{ $repositoryGenerator->baseInterface }};
@endif
@if (count($repositoryGenerator->traits) > 0)
@foreach($repositoryGenerator->traits as $trait)
use {{ $trait }};
@endforeach
@endif

/**
 * This is the repository class for table "{{ $repositoryGenerator->generatorForm->resourceTable }}".
 * Class {{ basename($repositoryGenerator->generatorForm->repositoryName) }}
 *
 * @package {{ class_namespace($repositoryGenerator->generatorForm->repositoryName) }}
<?= ' * @method ' . basename($repositoryGenerator->generatorForm->modelName) ?>|null findActive(array $params = [])
 */
class {{ basename($repositoryGenerator->generatorForm->repositoryName) }} extends {{ basename($repositoryGenerator->baseClass) }} {{ $repositoryGenerator->baseInterface ? 'implements ' . basename($repositoryGenerator->baseInterface) : '' }}
{
@if (count($repositoryGenerator->traits) > 0)
    use {{join(', ',  collect($repositoryGenerator->traits)->map(function ($trait) { return basename ($trait); })->toArray())}};

@endif
    protected function modelClass(): string
    {
        return {{ $repositoryGenerator->generatorForm->resourceName }}::class;
    }

}
