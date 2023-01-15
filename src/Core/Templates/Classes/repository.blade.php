<?php
/**
 * This is the template for generating the repository class of a specified table.
 */

/* @var $generator \Chatway\LaravelCrudGenerator\Core\Generators\RepositoryGenerator */
/* @var $generator->generatorForm->properties array list of properties (property => [type, name. comment]) */

$index = array_search('status', array_keys($generator->generatorForm->enums));

echo "<?php\n";
?>

namespace {{ class_namespace($generator->scenarioValue('repositoryName')) }};

@if ($generator->baseClass)
use {{ $generator->baseClass }};
@endif
use {{ $generator->scenarioValue('modelName') }};
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
@if ($generator->baseInterface)
use {{ $generator->baseInterface }};
@endif
@if ($index !== false)
use {{ $generator->generatorForm->enums['status']->enumName }};
@endif

@if (count($generator->traits) > 0)
@foreach($generator->traits as $trait)
use {{ $trait }};
@endforeach
@endif
/**
* This is the repository class for table "{{ $generator->generatorForm->resourceTable }}".
* Class {{ class_basename($generator->scenarioValue('repositoryName')) }}
*
* @package {{ class_namespace($generator->scenarioValue('repositoryName')) }}
<?= ' * @method ' . class_basename($generator->scenarioValue('modelName')) ?> []|Collection search(array $parameters = [], int $limit = null)
<?= ' * @method ' . class_basename($generator->scenarioValue('modelName')) ?> []|Collection searchActive(array $parameters = [], int $limit = null)
<?= ' * @method ' . class_basename($generator->scenarioValue('modelName')) ?>|null oneActive(array $params = [])
<?= ' * @method ' . class_basename($generator->scenarioValue('modelName')) ?>|null find(array $params = [])
<?= ' * @method ' . class_basename($generator->scenarioValue('modelName')) ?>|null findActive(array $params = [])
*/
class {{ class_basename($generator->scenarioValue('repositoryName')) }}{{ $generator->baseClass ? (' extends ' . class_basename($generator->baseClass)) : '' }}{{ $generator->baseInterface ? ' implements ' . class_basename($generator->baseInterface) : '' }}
{
@if (count($generator->traits) > 0)
    use {{join(', ',  collect($generator->traits)->map(function ($trait) { return class_basename($trait); })->toArray())}};

@endif
    protected function modelClass(): string
    {
        return {{ $generator->generatorForm->resourceName }}::class;
    }
@if($index !== false)
<?php
$activeStatusConst = in_array('active', $generator->generatorForm->enums['status']->types) ? 'ACTIVE' : ($generator->generatorForm->enums['status']->types[0] ?? 'ACTIVE');
$activeStatusConst = strtoupper($activeStatusConst);
$deletedStatusConst = in_array('deleted', $generator->generatorForm->enums['status']->types) ? 'DELETED' : ($generator->generatorForm->enums['status']->types[0] ?? 'DELETED');
$deletedStatusConst = strtoupper($deletedStatusConst);
?>
    protected function query(): Builder
    {
        return $this->newQuery()->where('status', '!=', {{class_basename($generator->generatorForm->enums['status']->enumName)}}::{{$deletedStatusConst}});
    }

    protected function active(): Builder
    {
        return $this->query()->where('status', '=', {{class_basename($generator->generatorForm->enums['status']->enumName)}}::{{$activeStatusConst}});
    }
@endif

    protected function applyParameters(Builder $query, array $parameters = []): Builder
    {
        return parent::applyParameters($query, $parameters)
            ->when(Arr::get($parameters, 'paramName'), fn(Builder $query, $value) => $query->where('paramName', $value))
            ;
    }
}
