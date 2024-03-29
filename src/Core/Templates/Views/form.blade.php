<?php
/**
 * This is the template for generating the controller class of a specified table.
 */

/* @var $generator \Chatway\LaravelCrudGenerator\Core\Generators\ViewGenerator */
/* @var $generator- >generatorForm->properties array list of properties (property => [type, name. comment]) */

echo "<?php\n";
?>

/**
 * @var \{{ $generator->scenarioValue('modelName') }} ${{ $generator->generatorForm->getResourceName(false, true, true) }}
 *
*/

?>
<div class="row justify-content-center">
    <div class="col-lg-10 card-wrapper">
        <div class="card">
            <div class="card-header">
                <h3 class="mb-0"><?= "{{ __('admin.sections.common') }" ?>}</h3>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-12">
@foreach($generator->generatorForm->properties as $property)
@if (!$property->inlet && $generator->renderedPropertyFormExist($property))
                        {!!  $generator->getRenderedPropertyForm($property)  !!}
@endif
@endforeach
                    </div>
                    <div class="col-md-12 text-right">
                        <?= "{{ BsForm::submit(__('admin.actions.submit')) }}" . PHP_EOL ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= "@include('admin.layouts.modals.cropper')" ?>
