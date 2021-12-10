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
@if ($generator->baseInterface)
use {{ $generator->baseInterface }};
@endif
@if ($index !== false)
use Illuminate\Database\Eloquent\Builder;
use {{ $generator->generatorForm->enums['status']->enumName }};
@endif
@if (count($generator->traits) > 0)
@foreach($generator->traits as $trait)
use {{ $trait }};
@endforeach
@endif

/**
 * This is the repository class for table "{{ $generator->generatorForm->resourceTable }}".
 * Class {{ basename($generator->generatorForm->repositoryName) }}
 *
 * @package {{ class_namespace($generator->generatorForm->repositoryName) }}
<?= ' * @method ' . basename($generator->generatorForm->modelName) ?>|null findActive(array $params = [])
 */
class {{ basename($generator->generatorForm->repositoryName) }} extends {{ basename($generator->baseClass) }} {{ $generator->baseInterface ? 'implements ' . basename($generator->baseInterface) : '' }}
{
@if (count($generator->traits) > 0)
    use {{join(', ',  collect($generator->traits)->map(function ($trait) { return basename ($trait); })->toArray())}};

@endif
    protected function modelClass(): string
    {
        return {{ $generator->generatorForm->resourceName }}::class;
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
        return $this->query()->where('status', '=', {{basename($generator->generatorForm->enums['status']->enumName)}}::{{$activeStatusConst}});
    }
@endif
}
