<?php
/**
 * This is the template for generating the controller class of a specified table.
 */

/* @var $generator \Chatway\LaravelCrudGenerator\Core\Generators\ViewGenerator */
/* @var $generator->generatorForm->properties array list of properties (property => [type, name. comment]) */
$variableName = $generator->generatorForm->getResourceName(false, true);
$routeName = $generator->generatorForm->getResourceName(true, true);
$modelName = $generator->generatorForm->modelName;
?>
<?= "@extends('admin.layouts.app', ['title' => __('admin.menu.$variableName')])" . PHP_EOL?>

<?= "@push('breadcrumbs')"?>

<?= "    <li class=\"breadcrumb-item\"><a href=\"{{ route('admin.$routeName.index') }}\">{{ __('admin.menu.$variableName') }}</a></li>"?>

<?= "    <li class=\"breadcrumb-item\">{{ Html::link(route('admin.$routeName.show', ['$variableName' => $$variableName]), \${$variableName}->title) }}</li>"?>

<?= "    <li class=\"breadcrumb-item active\">{{ __('admin.actions.updating') }}</li>"?>

<?= "@endpush"?>


<?= "@section('content')"?>

<?= "    {!! BsForm::put(['route' => ['admin.$routeName.update', '$variableName'=> $$variableName], 'files' => true]) !!}"?>

<?= "    @include('admin.$routeName.form')"?>

<?= "    {!! BsForm::close() !!}"?>

<?= "@endsection"?>
