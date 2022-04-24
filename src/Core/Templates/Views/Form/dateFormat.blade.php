<?php
/**
 * This is the template for generating the service class of a specified table.
 */
/* @var $generator \Chatway\LaravelCrudGenerator\Core\Entities\GeneratorForm */
/* @var $propertyDTO \Chatway\LaravelCrudGenerator\Core\DTO\PropertyDTO */
$propertyNameCamelCase = Str::camel($propertyDTO->name);
?>
<?= "{{ BsForm::date('$propertyNameCamelCase')
                                ->value(old('$propertyNameCamelCase', \${$generator->generatorForm->getResourceName(false, true)}->$propertyDTO->name))
                                ->placeholder(__('admin.columns.$propertyNameCamelCase'))
                                ->label(__('admin.columns.$propertyNameCamelCase'))
                        }}" . PHP_EOL ?>
