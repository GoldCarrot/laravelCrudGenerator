<?php
/**
 * This is the template for generating the service class of a specified table.
 */
/* @var $generator \Chatway\LaravelCrudGenerator\Core\Entities\GeneratorForm */
/* @var $propertyDTO \Chatway\LaravelCrudGenerator\Core\DTO\PropertyDTO */
$variableName = $generator->generatorForm->getResourceName(false, true, true);
$propertyNameCamelCase = Str::camel($propertyDTO->name)
?>
                        <div class="mt-3">
                            <label><?= "{{ __('admin.columns.$propertyNameCamelCase') }}" ?></label><br>
                            <span>
                                <?= "{{  $$variableName->$propertyDTO->name ? date('Y-m-d', strtotime($$variableName->$propertyDTO->name)) : null}}" ?>

                            </span>
                        </div>
