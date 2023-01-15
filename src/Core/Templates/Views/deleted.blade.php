<?php
/**
 * This is the template for generating the controller class of a specified table.
 */

/* @var $generator \Chatway\LaravelCrudGenerator\Core\Generators\ViewGenerator */
/* @var $generator- >generatorForm->properties array list of properties (property => [type, name. comment]) */
$variableName = $generator->generatorForm->getResourceName(false, true);
$routeName = $generator->generatorForm->getResourceName(true, true);
$modelName = $generator->scenarioValue('modelName');
?>
<?= "@extends('admin.layouts.app', ['title' => 'Удаленные записи ' . '__('admin.menu.$variableName')'])" ?>

<?= "@push('breadcrumbs')" . PHP_EOL ?>
    <?= "<li class=\"breadcrumb-item active\">{{ __('admin.menu.$variableName') }}</li>" . PHP_EOL ?>
<?= "@endpush" . PHP_EOL . PHP_EOL ?>

<?= "@section('content')" . PHP_EOL ?>
    <div class="row">
        <div class="col">
            <?= "@php". PHP_EOL ?>
            <?= "\$gridView = [
                'dataProvider' => \$dataProvider,
                'rowsFormAction' => route('admin.$routeName.create'),
                'title' => __('admin.menu.$variableName'),
                'useFilters' => true,
                'columnFields' => [
                    [
                        'label' => __('admin.columns.title'),
                        'attribute' => 'title',
                        'value' => function (\\$modelName \$$variableName) {
                            return mb_substr(\$$variableName"."->title, 0, 200);
                        },
                    ],
                    [
                        'class' => \Itstructure\GridView\Columns\ActionColumn::class,
                        'actionTypes' => [
                            'view' => function (\\$modelName \$$variableName) {
                                return route('admin.$routeName.show', ['$variableName' => \$$variableName]);
                            },
                            'edit' => function (\\$modelName \$$variableName) {
                                return route('admin.$routeName.edit', ['$variableName' => \$$variableName]);
                            },
                            'delete' => function (\\$modelName \$$variableName) {
                                return route('admin.$routeName.destroy', ['$variableName' => \$$variableName]);
                            },
                        ]
                    ]
                ]
            ];" . PHP_EOL?>
            <?= "@endphp" . PHP_EOL ?>
            <?= "@gridView(\$gridView)" . PHP_EOL ?>
        </div>
    </div>
<?= "@endsection" ?>

