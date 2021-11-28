<?php
/**
 * This is the template for generating the model class of a specified table.
 */

/* @var $modelGenerator->generatorForm \Chatway\LaravelCrudGenerator\Core\Entities\GeneratorForm */
/* @var $modelGenerator \Chatway\LaravelCrudGenerator\Core\Generators\ModelGenerator */
/* @var $modelGenerator->generatorForm->properties array list of properties (property => [type, name. comment]) */

echo "<?php\n";
?>
namespace {{ $modelGenerator->generatorForm->getNsByClassName($modelGenerator->generatorForm->modelName) }};


use {{ $modelGenerator->baseClass }};
use Eloquent;
<?php /** Начало прикрепления классов для внутренних ключей **/ ?>
<?php $addedClasses = [] ?>
@foreach($modelGenerator->generatorForm->internalForeignKeys as $internalForeignKey)
@if ($internalForeignKey['className'] != $modelGenerator->generatorForm->modelName && !in_array($internalForeignKey['className'], $addedClasses))
@php($addedClasses[] = $internalForeignKey['className'])
use {{ $internalForeignKey['className'] }};
@endif
@endforeach
<?php /** Конец прикрепления классов для внутренних ключей **/ ?>
<?php /** Начало прикрепления классов для внешних ключей **/ ?>
@foreach($modelGenerator->generatorForm->extrernalForeignKeys as $externalForeignKey)
@if ($externalForeignKey['className'] != $modelGenerator->generatorForm->modelName && !in_array($externalForeignKey['className'], $addedClasses))
@php($addedClasses[] = $externalForeignKey['className'])
use {{ $externalForeignKey['className'] }};
@endif
@endforeach
<?php /** Конец прикрепления классов для внешних ключей **/ ?>
<?=  $modelGenerator->generatorForm->carbonIsset ? 'use Carbon\Carbon;' . "\n" : '' ?>

/**
 * This is the model class for table "{{ $modelGenerator->generatorForm->resourceTable }}".
 * Class {{ $modelGenerator->generatorForm->resourceName }}
 *
 * @package {{ $modelGenerator->generatorForm->modelName }}
 * @mixin Eloquent
@foreach($modelGenerator->generatorForm->properties as $property)
 * @property {{ $modelGenerator->generatorForm->getFormattedProperty($property->type, $property->name) }}
@endforeach
 *
@foreach($modelGenerator->generatorForm->internalForeignKeys as $internalForeignKey)
 * @property {{ $modelGenerator->generatorForm->getFormattedProperty(basename($internalForeignKey['className']), Str::singular($internalForeignKey['tableName'])) }}
@endforeach
 *
@foreach($modelGenerator->generatorForm->extrernalForeignKeys as $externalForeignKey)
 * @property {{ $modelGenerator->generatorForm->getFormattedProperty(basename($externalForeignKey['className']) .' []', Str::pluralStudly(lcfirst(class_basename($externalForeignKey['className'])))) }}
@endforeach
*/

class {{ $modelGenerator->generatorForm->resourceName }} extends {{ basename($modelGenerator->baseClass) }}
{
    protected $table = '{{ $modelGenerator->generatorForm->resourceTable }}';
    {!!  count($modelGenerator->generatorForm->dateProperties) > 0 ? "\n    protected \$dates = ['" . implode("', '", $modelGenerator->generatorForm->dateProperties) . "'];" : "" !!}
@foreach($modelGenerator->generatorForm->internalForeignKeys as $internalForeignKey)

    public function {{ Str::singular(str_replace('_id', '', $internalForeignKey['tableName'])) }}()
    {
        return $this->hasOne({{ basename($internalForeignKey['className']) }}::class, 'id', '{{ $internalForeignKey['columnName'] }}');
    }
@endforeach
@foreach($modelGenerator->generatorForm->extrernalForeignKeys as $externalForeignKey)

    public function {{ Str::pluralStudly(lcfirst(class_basename($externalForeignKey['className']))) }}()
    {
        return $this->belongsToMany({{ basename($externalForeignKey['className']) }}::class, '{{ $externalForeignKey['tableName'] }}');
    }
@endforeach
}