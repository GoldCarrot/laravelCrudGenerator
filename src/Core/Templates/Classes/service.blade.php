<?php
/**
 * This is the template for generating the service class of a specified table.
 */

/* @var $serviceGenerator->generatorForm \Chatway\LaravelCrudGenerator\Core\Entities\GeneratorForm */
/* @var $serviceGenerator \Chatway\LaravelCrudGenerator\Core\Generators\ServiceGenerator */
/* @var $serviceGenerator->generatorForm->properties array list of properties (property => [type, name. comment]) */

echo "<?php\n";
?>

namespace {{ $serviceGenerator->generatorForm->getServiceNs() }};


use {{ $serviceGenerator->getBaseClassWithNs() }};
use {{ $serviceGenerator->generatorForm->getModelFullName() }};
@if ($serviceGenerator->getBaseInterfaceWithNs())
use {{ $serviceGenerator->getBaseInterfaceWithNs() }};
@endif
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

/**
 * This is the service class for table "{{ $serviceGenerator->generatorForm->resourceTable }}".
 * Class {{ $serviceGenerator->generatorForm->getServiceName() }}
 *
 * @package {{ $serviceGenerator->generatorForm->getServiceNs() }}
<?= ' * @method ' . $serviceGenerator->generatorForm->getModelName() ?>|null findActive(array $params = [])
*/

class {{ $serviceGenerator->generatorForm->getServiceName() }} extends {{ $serviceGenerator->baseClass }} {{ $serviceGenerator->getBaseInterfaceWithNs() ? 'implements ' . $serviceGenerator->baseInterface : '' }}
{
    public function create(array $data): {{ $serviceGenerator->generatorForm->resourceName }}
    {
        $new = new {{ $serviceGenerator->generatorForm->resourceName }}();
        return $this->update($new, $data);
    }

    public function update({{ $serviceGenerator->generatorForm->resourceName }}|Model $model, array $data): {{ $serviceGenerator->generatorForm->resourceName }}
    {
@foreach($serviceGenerator->generatorForm->properties as $property)
@if(!$property->inlet)
{!! $serviceGenerator->getFormattedProperty($property) !!}
@endif
        @endforeach

        $model->saveOrFail();

@foreach($serviceGenerator->generatorForm->extrernalForeignKeys as $externalForeignKey)
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
