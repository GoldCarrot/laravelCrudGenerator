<?php
/**
 * This is the template for generating the repository class of a specified table.
 */

/* @var $repositoryGenerator->generatorForm \Chatway\LaravelCrudGenerator\Core\Entities\GeneratorForm */
/* @var $repositoryGenerator \Chatway\LaravelCrudGenerator\Core\Generators\RepositoryGenerator */
/* @var $repositoryGenerator->generatorForm->properties array list of properties (property => [type, name. comment]) */

echo "<?php\n";
?>

namespace {{ $repositoryGenerator->generatorForm->getRepositoryNs() }};


use {{ $repositoryGenerator->getBaseClassWithNs() }};
use {{ $repositoryGenerator->generatorForm->getModelFullName() }};

/**
 * This is the repository class for table "{{ $repositoryGenerator->generatorForm->resourceTable }}".
 * Class {{ $repositoryGenerator->generatorForm->getRepositoryName() }}
 *
 * @package {{ $repositoryGenerator->generatorForm->getRepositoryNs() }}
<?= ' * @method ' . $repositoryGenerator->generatorForm->getModelName() ?>|null findActive(array $params = [])
*/

class {{ $repositoryGenerator->generatorForm->getRepositoryName() }} extends {{ $repositoryGenerator->baseClass }}
{
    protected function modelClass(): string
    {
        return {{ $repositoryGenerator->generatorForm->resourceName }}::class;
    }
}
