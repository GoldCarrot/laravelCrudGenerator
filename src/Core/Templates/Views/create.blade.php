<?php
/**
 * This is the template for generating the view create
 */

/* @var $generator \Chatway\LaravelCrudGenerator\Core\Generators\ViewGenerator */

?>
@@extends('admin.layouts.app', ['title' => ''])


@@push('breadcrumbs')
<?=    '    <li class="breadcrumb-item"><a href="{{ route(\'admin.'
    . $generator->generatorForm->getResourceName(true, true, true) . '.index\') }}">{{ __(\'admin.menu.'
    . $generator->generatorForm->getResourceName(false, true, true) . '\') }}</a></li>'
    . PHP_EOL .
    '    <li class="breadcrumb-item active">{{ __(\'admin.actions.creating\') }}</li>' . PHP_EOL ?>
@@endpush

<?= '@section(\'content\')' . PHP_EOL .
    '    {!! BsForm::post([\'route\' => \'admin.'
    . $generator->generatorForm->getResourceName(true, true, true) . '.store\', \'files\' => true]) !!}' . PHP_EOL .
    '    @include(\'admin.'
    . $generator->generatorForm->getResourceName(true, true, true) . '.form\')' . PHP_EOL .
    '    {!! BsForm::close() !!}' . PHP_EOL .
    '@endsection' ?>

