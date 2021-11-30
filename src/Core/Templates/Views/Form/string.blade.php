<?php
/**
 * This is the template for generating the service class of a specified table.
 */
/* @var $generator \Chatway\LaravelCrudGenerator\Core\Entities\GeneratorForm */
/* @var $propertyDTO \Chatway\LaravelCrudGenerator\Core\DTO\PropertyDTO */
?>
<?= "{{ BsForm::text('$propertyDTO->name')
                                ->value(old('$propertyDTO->name', \${$generator->generatorForm->getResourceName(false, true)}->$propertyDTO->name))
                                ->placeholder(__('admin.columns.$propertyDTO->name'))
                                ->label(__('admin.columns.$propertyDTO->name'))
                        }}" . PHP_EOL ?>
