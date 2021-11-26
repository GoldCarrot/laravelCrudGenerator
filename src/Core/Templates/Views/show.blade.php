<?php
/**
 * This is the template for generating the controller class of a specified table.
 */

/* @var $viewGenerator- >generatorForm \Chatway\LaravelCrudGenerator\Core\Entities\GeneratorForm */
/* @var $viewGenerator \Chatway\LaravelCrudGenerator\Core\Generators\ViewGenerator */
/* @var $viewGenerator- >generatorForm->properties array list of properties (property => [type, name. comment]) */
$variableName = $viewGenerator->generatorForm->getResourceName(false, true);
$routeName = $viewGenerator->generatorForm->getResourceName(true, true);
$modelName = $viewGenerator->generatorForm->modelName;
echo "<?php\n";
?>
/**
 * @var \{{ $viewGenerator->generatorForm->modelName }} ${{ $viewGenerator->generatorForm->getResourceName(false, true) }}
 *
 */
?>
<?= "@extends('admin.layouts.app', ['title' => __('admin.menu.$variableName')])" ?>

<?= "@push('breadcrumbs')"?>
    <?= "<li class=\"breadcrumb-item\"><a href=\"{{ route('admin.$routeName.index') }}\">{{ __('admin.menu.$variableName') }}</a></li>"?>
    <?= "<li class=\"breadcrumb-item active\">{{ \${$variableName}->title }}</li>"?>
<?= "@endpush"?>

<?= "@section('content')"?>
    <div class="row justify-content-center">
        <div class="col-lg-8 card-wrapper">
            <div class="card">
                <div class="card-body">
                    <div class="pt-4">
                        @foreach($viewGenerator->generatorForm->properties as $property)
                            @if (!$property->inlet && $viewGenerator->renderedPropertyShowExist($property))
                                {!!  $viewGenerator->getRenderedPropertyShow($property)  !!}
                            @endif
                        @endforeach
                    </div>
                    <div class="pt-6 text-right">
                        <?= "{{ Html::link(route('admin.$routeName.edit', ['$variableName' => $$variableName]), __('admin.actions.update'), ['class' => 'btn btn-primary']) }}". PHP_EOL ?>
                        <?= "{{ BsForm::open(['url' => route('admin.$routeName.destroy', ['$variableName' => $$variableName]), 'method' => 'delete', 'style' => 'display: inline-block']) }}". PHP_EOL ?>
                        <button class="btn btn-danger" data-toggle="tooltip" data-placement="top"
                                title="<?= "{{ __('admin.actions.destroy') }}"?>">
                                <?= "{{ __('admin.actions.destroy') }}"?>
                        </button>
                            <?= "{{ BsForm::close() }}"?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?= "@endsection"?>

<?= "@include('admin.layouts.modals.cropper')"?>
