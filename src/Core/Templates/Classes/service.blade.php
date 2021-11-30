<?php
/**
 * This is the template for generating the service class of a specified table.
 */

/* @var $serviceGenerator \Chatway\LaravelCrudGenerator\Core\Generators\ServiceGenerator */
/* @var $serviceGenerator->generatorForm->properties array list of properties (property => [type, name. comment]) */

echo "<?php\n";
?>

namespace {{ class_namespace($serviceGenerator->generatorForm->serviceName) }};


use {{ $serviceGenerator->baseClass }};
use {{ $serviceGenerator->generatorForm->modelName }};
@if ($serviceGenerator->baseInterface)
use {{ $serviceGenerator->baseInterface }};
@endif
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

/**
 * This is the service class for table "{{ $serviceGenerator->generatorForm->resourceTable }}".
 * Class {{ $serviceGenerator->generatorForm->serviceName }}
 *
 * @package {{ class_namespace($serviceGenerator->generatorForm->serviceName) }}
<?= ' * @method ' . basename($serviceGenerator->generatorForm->modelName) ?>|null findActive(array $params = [])
 */
class {{ basename($serviceGenerator->generatorForm->serviceName) }} extends {{ basename($serviceGenerator->baseClass) }} {{ $serviceGenerator->baseInterface ? 'implements ' . basename($serviceGenerator->baseInterface) : '' }}
{
    public function create(array $data): {{ $serviceGenerator->generatorForm->resourceName }}
    {
        $new = new {{ $serviceGenerator->generatorForm->resourceName }}();
        return $this->update($new, $data);
    }

    public function update({{ $serviceGenerator->generatorForm->resourceName }}|Model $model, array $data): {{ $serviceGenerator->generatorForm->resourceName }}
    {
@foreach($serviceGenerator->generatorForm->properties as $property)
@if(($property->name == 'alias' || $property->name == 'slug') && isset($serviceGenerator->generatorForm->properties['title']))
        {!! "\$model->{$property->name} = Arr::get(\$data, '$property->name', \$model->$property->name) ?: \$this->slug(\$model, \$model->title, \$model->id, '$property->name');" !!}
@continue
@endif
@if(!$property->inlet)
        {!! "\$model->{$property->name} = Arr::get(\$data, '{$property->name}', \$model->{$property->name});" !!}
@continue
@endif
@endforeach

        $model->saveOrFail();

@foreach($serviceGenerator->generatorForm->externalForeignKeys as $externalForeignKey)
if (isset($data['{!! Str::pluralStudly(lcfirst(class_basename($externalForeignKey['className']))) !!}List'])) {
            $model->{!! Str::pluralStudly(lcfirst(class_basename($externalForeignKey['className']))) !!}()->detach($model->{!! Str::pluralStudly(lcfirst(class_basename($externalForeignKey['className']))) !!});
            $model->{!! Str::pluralStudly(lcfirst(class_basename($externalForeignKey['className']))) !!}()->attach($data['{!! Str::pluralStudly(lcfirst(class_basename($externalForeignKey['className']))) !!}List']);
        }

        @endforeach
        return $model;
    }

    public function destroy({{ $serviceGenerator->generatorForm->resourceName }}|Model $model): bool
    {
        return $model->forceFill(['status' => 'deleted'])->save();
    }
}
