<?php
/**
 * This is the template for generating the model class of a specified table.
 */

/* @var $generator \Chatway\LaravelCrudGenerator\Core\Generators\ModelGenerator */
/* @var $generator->generatorForm->properties array list of properties (property => [type, name. comment]) */

echo "<?php\n";
?>
namespace {{ $generator->generatorForm->getNsByClassName($generator->generatorForm->modelName) }};


use {{ $generator->baseClass }};
use Eloquent;
<?php /** Начало прикрепления классов для внутренних ключей **/ ?>
<?php $addedClasses = [] ?>
@foreach($generator->generatorForm->internalForeignKeys as $internalForeignKey)
@if ($internalForeignKey['className'] != $generator->generatorForm->modelName && !in_array($internalForeignKey['className'], $addedClasses))
@php($addedClasses[] = $internalForeignKey['className'])
use {{ $internalForeignKey['className'] }};
@endif
@endforeach
<?php /** Конец прикрепления классов для внутренних ключей **/ ?>
<?php /** Начало прикрепления классов для внешних ключей **/ ?>
@foreach($generator->generatorForm->externalForeignKeys as $externalForeignKey)
@if ($externalForeignKey['className'] != $generator->generatorForm->modelName && !in_array($externalForeignKey['className'], $addedClasses))
@php($addedClasses[] = $externalForeignKey['className'])
use {{ $externalForeignKey['className'] }};
@endif
@endforeach
<?php /** Конец прикрепления классов для внешних ключей **/ ?>
<?=  $generator->generatorForm->carbonIsset ? 'use Carbon\Carbon;' . "\n" : '' ?>

/**
 * This is the model class for table "{{ $generator->generatorForm->resourceTable }}".
 * Class {{ $generator->generatorForm->resourceName }}
 *
 * @package {{ $generator->generatorForm->modelName }}
 * @mixin Eloquent
@foreach($generator->generatorForm->properties as $property)
 * @property {{ $generator->generatorForm->getFormattedProperty($property->type, $property->name) }}
@endforeach
 *
@foreach($generator->generatorForm->internalForeignKeys as $internalForeignKey)
 * @property {{ $generator->generatorForm->getFormattedProperty(basename($internalForeignKey['className']), Str::singular($internalForeignKey['tableName'])) }}
@endforeach
 *
@foreach($generator->generatorForm->externalForeignKeys as $externalForeignKey)
 * @property {{ $generator->generatorForm->getFormattedProperty(basename($externalForeignKey['className']) .' []', Str::pluralStudly(lcfirst(class_basename($externalForeignKey['className'])))) }}
@endforeach
*/

class {{ $generator->generatorForm->resourceName }} extends {{ basename($generator->baseClass) }}
{
    protected $table = '{{ $generator->generatorForm->resourceTable }}';
    {!!  count($generator->generatorForm->dateProperties) > 0 ? "\n    protected \$dates = ['" . implode("', '", $generator->generatorForm->dateProperties) . "'];" : "" !!}
@foreach($generator->generatorForm->internalForeignKeys as $internalForeignKey)

    public function {{ Str::singular(str_replace('_id', '', $internalForeignKey['tableName'])) }}()
    {
        return $this->hasOne({{ basename($internalForeignKey['className']) }}::class, 'id', '{{ $internalForeignKey['columnName'] }}');
    }
@endforeach
@foreach($generator->generatorForm->externalForeignKeys as $externalForeignKey)

    public function {{ Str::pluralStudly(lcfirst(class_basename($externalForeignKey['className']))) }}()
    {
        return $this->belongsToMany({{ basename($externalForeignKey['className']) }}::class, '{{ $externalForeignKey['tableName'] }}');
    }
@endforeach
}
