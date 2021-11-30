<?php
/**
 * This is the template for generating the service class of a specified table.
 */
/* @var $generator \Chatway\LaravelCrudGenerator\Core\Entities\GeneratorForm */
$fieldName = 'title';
?>
<?= "{{ BsForm::text('$fieldName')
                                ->value(old('$fieldName', \${$generator->generatorForm->getResourceName(false, true)}->$fieldName))
                                ->placeholder(__('admin.columns.$fieldName'))
                                ->label(__('admin.columns.$fieldName'))
                        }}" . PHP_EOL ?>
