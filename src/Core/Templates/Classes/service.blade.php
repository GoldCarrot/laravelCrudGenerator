<?php
/**
 * This is the template for generating the service class of a specified table.
 */

/* @var $generator \Chatway\LaravelCrudGenerator\Core\Generators\ServiceGenerator */
/* @var $generator->generatorForm->properties array list of properties (property => [type, name. comment]) */

echo "<?php\n";
?>

namespace {{ class_namespace($generator->generatorForm->serviceName) }};


use {{ $generator->baseClass }};
use {{ $generator->generatorForm->modelName }};
@if ($generator->baseInterface)
use {{ $generator->baseInterface }};
@endif
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

/**
 * This is the service class for table "{{ $generator->generatorForm->resourceTable }}".
 * Class {{ $generator->generatorForm->serviceName }}
 *
 * @package {{ class_namespace($generator->generatorForm->serviceName) }}
<?= ' * @method ' . class_basename($generator->generatorForm->modelName) ?>|null findActive(array $params = [])
 */
class {{ class_basename($generator->generatorForm->serviceName) }} extends {{ class_basename($generator->baseClass) }} {{ $generator->baseInterface ? 'implements ' . class_basename($generator->baseInterface) : '' }}
{
    public function create(array $data): {{ $generator->generatorForm->resourceName }}
    {
        $model = new {{ $generator->generatorForm->resourceName }}();
        return $this->update($model, $data);
    }

    public function update({{ $generator->generatorForm->resourceName }}|Model $model, array $data): {{ $generator->generatorForm->resourceName }}
    {
@foreach($generator->generatorForm->properties as $property)
    @php
        $propertyCamelCase = Str::camel($property->name)
    @endphp
@if(($property->name == 'alias' || $property->name == 'slug') && isset($generator->generatorForm->properties['title']))
        {!! "\$model->{$property->name} = Arr::get(\$data, '$propertyCamelCase', \$model->$property->name) ?: \$this->slug(\$model, \$model->title, \$model->id, '$property->name');" !!}
@continue
@endif
@if(!$property->inlet)

        {!! "\$model->{$property->name} = Arr::get(\$data, '{$propertyCamelCase}', \$model->{$property->name});" !!}
@continue
@endif
@endforeach

        $model->saveOrFail();

@foreach($generator->generatorForm->externalForeignKeys as $externalForeignKey)
if (isset($data['{!! Str::pluralStudly(lcfirst(class_basename($externalForeignKey['className']))) !!}List'])) {
            $model->{!! Str::pluralStudly(lcfirst(class_basename($externalForeignKey['className']))) !!}()->detach($model->{!! Str::pluralStudly(lcfirst(class_basename($externalForeignKey['className']))) !!});
            $model->{!! Str::pluralStudly(lcfirst(class_basename($externalForeignKey['className']))) !!}()->attach($data['{!! Str::pluralStudly(lcfirst(class_basename($externalForeignKey['className']))) !!}List']);
        }

        @endforeach
        return $model;
    }

    public function destroy({{ $generator->generatorForm->resourceName }}|Model $model): bool
    {
        return $model->forceFill(['status' => 'deleted'])->save();
    }
}
