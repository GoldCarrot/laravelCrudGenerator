<?php
/**
 * This is the template for generating the repository class of a specified table.
 */

/* @var $generator \Chatway\LaravelCrudGenerator\Core\Generators\RepositoryGenerator */
/* @var $generator->generatorForm->properties array list of properties (property => [type, name. comment]) */

$index = array_search('status', array_keys($generator->generatorForm->enums));

echo "<?php\n";
?>

namespace {{ class_namespace($generator->generatorForm->repositoryName) }};


use {{ $generator->baseClass }};
use {{ $generator->generatorForm->modelName }};
use Illuminate\Support\Arr;
@if ($generator->baseInterface)
use {{ $generator->baseInterface }};
@endif
@if ($index !== false)
use Illuminate\Database\Eloquent\Builder;
use {{ $generator->generatorForm->enums['status']->enumName }};
@endif
use Illuminate\Support\Collection;
@if (count($generator->traits) > 0)
@foreach($generator->traits as $trait)
use {{ $trait }};
@endforeach
@endif

/**
 * This is the repository class for table "{{ $generator->generatorForm->resourceTable }}".
 * Class {{ class_basename($generator->generatorForm->repositoryName) }}
 *
 * @package {{ class_namespace($generator->generatorForm->repositoryName) }}
<?= ' * @method ' . class_basename($generator->generatorForm->modelName) ?> []|Collection search(array $parameters = [], int $limit = null)
<?= ' * @method ' . class_basename($generator->generatorForm->modelName) ?> []|Collection searchActive(array $parameters = [], int $limit = null)
<?= ' * @method ' . class_basename($generator->generatorForm->modelName) ?>|null oneActive(array $params = [])
<?= ' * @method ' . class_basename($generator->generatorForm->modelName) ?>|null find(array $params = [])
<?= ' * @method ' . class_basename($generator->generatorForm->modelName) ?>|null findActive(array $params = [])
 */
class {{ class_basename($generator->generatorForm->repositoryName) }} extends {{ class_basename($generator->baseClass) }} {{ $generator->baseInterface ? 'implements ' . class_basename($generator->baseInterface) : '' }}
{
@if (count($generator->traits) > 0)
    use {{join(', ',  collect($generator->traits)->map(function ($trait) { return class_basename($trait); })->toArray())}};

@endif
    protected function modelClass(): string
    {
        return {{ $generator->generatorForm->resourceName }}::class;
    }

    protected function make(): static
    {
        return new static();
    }

    public function getLast($limit = 15)
    {
        return $this->active()->latest('columnName')->orderBy('columnName', 'ASC')->orderBy('id', 'DESC')->limit($limit)->get();
    }
@if (isset($generator->generatorForm->properties['alias']) || isset($generator->generatorForm->properties['slug']))
<?php $field = $generator->generatorForm->properties['alias']->name ?? $generator->generatorForm->properties['slug']->name ?>

    public function getBy{{ucfirst($field)}}(${{$field}})
    {
        return $this->active()->where('{{$field}}', '=', ${{$field}})->first();
    }

@endif
@if($index !== false)
<?php
    $activeStatusConst = in_array('active', $generator->generatorForm->enums['status']->types) ? 'ACTIVE' : ($generator->generatorForm->enums['status']->types[0] ?? 'active');
    $activeStatusConst = strtoupper($activeStatusConst);
?>

    protected function active(): Builder
    {
        return $this->query()->where('status', '=', {{class_basename($generator->generatorForm->enums['status']->enumName)}}::{{$activeStatusConst}});
    }
@endif

    protected function applyParameters(Builder $query, array $parameters = []): Builder
    {
        return parent::applyParameters($query, $parameters)
            ->when(Arr::get($parameters, 'paramName'), fn(Builder $query, $value) => $query->where('paramName', $value));
    }

    /**
     * @return Collection
     */
    public function getArrayForSelect(): Collection
    {
        return $this->active()->get()->mapWithKeys(function ($item) {
            return [$item['id'] => trim($item['title'])];
        });
    }
}
