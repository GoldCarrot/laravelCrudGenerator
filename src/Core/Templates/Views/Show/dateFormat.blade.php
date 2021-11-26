<?php
/**
 * This is the template for generating the service class of a specified table.
 */
/* @var $viewGenerator \Chatway\LaravelCrudGenerator\Core\Entities\GeneratorForm */
/* @var $propertyDTO \Chatway\LaravelCrudGenerator\Core\DTO\PropertyDTO */
$variableName = $viewGenerator->generatorForm->getResourceName(false, true);
?>
<div class="mt-3">
    <label><?= "{{ __('admin.columns.$propertyDTO->name') }}" ?></label><br>
    <span>
                                <?= "{\{$$variableName->date_finish ? date('Y-m-d', strtotime($this->date_finish)) : null}}" ?>
                            </span>
</div>
