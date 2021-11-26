<?php
/**
 * This is the template for generating the service class of a specified table.
 */
/* @var $viewGenerator \Chatway\LaravelCrudGenerator\Core\Entities\GeneratorForm */
$fieldName = 'url';
?>
<?= "{{ BsForm::text('$fieldName')
                                ->value(old('$fieldName', \${$viewGenerator->generatorForm->getResourceName(false, true)}->$fieldName))
                                ->placeholder(__('admin.columns.$fieldName'))
                                ->label(__('admin.columns.$fieldName'))
                        }}" . PHP_EOL ?>
