<?php
/**
 * This is the template for generating the service class of a specified table.
 */

/* @var $generator \Chatway\LaravelCrudGenerator\Core\Generators\ViewGenerator */
/* @var $propertyDTO \Chatway\LaravelCrudGenerator\Core\DTO\PropertyDTO */
$firstType = strtoupper($propertyDTO->enum->getFirstType());
$enumName = $propertyDTO->enum->enumName;
$propertyNameCamelCase = Str::camel($propertyDTO->name)
?>
<?= "{{ BsForm::select('$propertyNameCamelCase', \\{$enumName}::labels())
                                ->value(old('$propertyNameCamelCase', \${$generator->generatorForm->getResourceName(false, true)}->$propertyDTO->name) ?: \\{$enumName}::$firstType)
                                ->placeholder(__('admin.columns.{$propertyNameCamelCase}'))
                                ->label(__('admin.columns.{$propertyNameCamelCase}'))
                        }}" . PHP_EOL ?>
