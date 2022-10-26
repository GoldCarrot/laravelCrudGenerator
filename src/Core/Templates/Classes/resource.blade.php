<?php
/**
 * This is the template for generating the controller class of a specified table.
 */

/* @var $generator \Chatway\LaravelCrudGenerator\Core\Generators\ResourceGenerator */
/* @var $generator->generatorForm->properties array list of properties (property => [type, name. comment]) */

echo "<?php\n";
?>

namespace {{ class_namespace($generator->generatorForm->resourceClassName) }};

use {{ $generator->generatorForm->modelName }};
use Illuminate\Http\Resources\Json\JsonResource;
@foreach($generator->generatorForm->properties as $property)
@if($generator->getUse($property))
use {!!  $generator->getUse($property)  !!};
@endif
@if($property->isEnum)
use {!!  $property->enum->enumName  !!};
@endif
@endforeach
@foreach($generator->generatorForm->externalForeignKeys as $index => $externalForeignKey)
@if($generator->getUseChildren($externalForeignKey))
use {!!  $generator->getUseChildren($externalForeignKey)  !!};
@endif
@endforeach
/**
 * This is the resource api for table "{{ $generator->generatorForm->resourceTable }}".
 * Class {{ class_basename($generator->generatorForm->resourceClassName) }}
 *
 * @property {{ class_basename($generator->generatorForm->modelName) }} $resource
*/

class {{ class_basename($generator->generatorForm->resourceClassName) }} extends JsonResource
{
    public function toArray($request): array
    {
        return [
@foreach($generator->generatorForm->properties as $property)
@if(!$property->inlet && !in_array($property->name, ['status', 'sort']))
            {!!  $generator->getFormattedRule($property)  !!}
@endif
@endforeach
@foreach($generator->generatorForm->externalForeignKeys as $index => $externalForeignKey)
            {!!  $generator->getChildren($externalForeignKey)  !!}
@endforeach
        ];
    }
}
