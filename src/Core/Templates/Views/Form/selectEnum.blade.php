<?php
/**
 * This is the template for generating the service class of a specified table.
 */

/* @var $viewGenerator \Chatway\LaravelCrudGenerator\Core\Generators\ViewGenerator */
/* @var $propertyDTO \Chatway\LaravelCrudGenerator\Core\DTO\PropertyDTO */
?>
<?= "{{ BsForm::select('$propertyDTO->name', \\{$propertyDTO->enum->enumName}::labels())
                                ->value(old('$propertyDTO->name'))
                                ->placeholder(__('admin.columns.{$propertyDTO->name}'))
                                ->label(__('admin.columns.{$propertyDTO->name}'))
                        }}" . PHP_EOL ?>
