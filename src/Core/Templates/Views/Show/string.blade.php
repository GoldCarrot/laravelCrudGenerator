<?php
/**
 * This is the template for generating the service class of a specified table.
 */
/* @var $viewGenerator \Chatway\LaravelCrudGenerator\Core\Entities\GeneratorForm */
/* @var $propertyDTO \Chatway\LaravelCrudGenerator\Core\DTO\PropertyDTO */
?>
<div class="mt-3">
    <?= "<label>{{ __('admin.columns.$propertyDTO->name') }}</label><br>" ?>
    <span>
                                <?= "{{\${$viewGenerator->generatorForm->getResourceName(false, true)}->$propertyDTO->name}}" ?>
                            </span>
</div>
