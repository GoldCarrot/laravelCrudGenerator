<?php
/**
 * This is the template for generating the service class of a specified table.
 */

/* @var $generator \Chatway\LaravelCrudGenerator\Core\Generators\ViewGenerator */
/* @var $propertyDTO \Chatway\LaravelCrudGenerator\Core\DTO\PropertyDTO */
$firstType = strtoupper($propertyDTO->enum->getFirstType());
$enumName = $propertyDTO->enum->enumName;
?>
<?= "{{ BsForm::select('$propertyDTO->name', \\{$enumName}::labels())
                                ->value(old('$propertyDTO->name', \${$generator->generatorForm->getResourceName(false, true)}->$propertyDTO->name) ?: \\{$enumName}::$firstType)
                                ->placeholder(__('admin.columns.{$propertyDTO->name}'))
                                ->label(__('admin.columns.{$propertyDTO->name}'))
                        }}" . PHP_EOL ?>
