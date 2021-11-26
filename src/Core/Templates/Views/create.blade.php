<?php
/**
 * This is the template for generating the view create
 */

/* @var $viewGenerator- >generatorForm \Chatway\LaravelCrudGenerator\Core\Entities\GeneratorForm */
/* @var $viewGenerator \Chatway\LaravelCrudGenerator\Core\Generators\ViewGenerator */

?>
<?= '@extends(\'admin.layouts.app\', [\'title\' => __(\'admin.menu.'
    . $viewGenerator->generatorForm->getResourceName(false, true) . '\')])' ?>


<?= '@push(\'breadcrumbs\')' . PHP_EOL .
    '    <li class="breadcrumb-item"><a href="{{ route(\'admin.'
    . $viewGenerator->generatorForm->getResourceName(true, true) . '.index\') }}">{{ __(\'admin.menu.'
    . $viewGenerator->generatorForm->getResourceName(false, true) . '\') }}</a></li>'
    . PHP_EOL .
    '    <li class="breadcrumb-item active">{{ __(\'admin.actions.creating\') }}</li>' . PHP_EOL .
    '@endpush' ?>

<?= '@section(\'content\')' . PHP_EOL .
    '    {!! BsForm::post([\'route\' => \'admin.'
    . $viewGenerator->generatorForm->getResourceName(true, true) . '.store\', \'files\' => true]) !!}' . PHP_EOL .
    '    @include(\'admin.'
    . $viewGenerator->generatorForm->getResourceName(true, true) . '.form\')' . PHP_EOL .
    '    {!! BsForm::close() !!}' . PHP_EOL .
    '@endsection' ?>

