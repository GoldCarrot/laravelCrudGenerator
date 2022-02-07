<?php
/**
 * This is the template for generating the controller class of a specified table.
 */

/* @var $generator \Chatway\LaravelCrudGenerator\Core\Generators\PresenterGenerator */
/* @var $generator->generatorForm->properties array list of properties (property => [type, name. comment]) */

echo "<?php\n";
?>

namespace {{ class_namespace($generator->generatorForm->presenterName) }};

use {{ $generator->generatorForm->modelName }};

/**
 * This is the presenter api for table "{{ $generator->generatorForm->resourceTable }}".
 * Class {{ class_basename($generator->generatorForm->presenterName) }}
 *
 * @package \{{ $generator->generatorForm->presenterName }}
*/

class {{ class_basename($generator->generatorForm->presenterName) }}
{
    protected {{ class_basename($generator->generatorForm->modelName) }} $model;

    public function __construct($model)
    {
        $this->model = $model;
    }


    public function toArray(): array
    {
        return [
@foreach($generator->generatorForm->properties as $property)
@if($property->name != 'id' && !$property->inlet)
            {!!  $generator->getFormattedRule($property)  !!}
@endif
@endforeach
        ];
    }
}
