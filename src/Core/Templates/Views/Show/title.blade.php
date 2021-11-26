<?php
/**
 * This is the template for generating the
 */
/* @var $viewGenerator \Chatway\LaravelCrudGenerator\Core\Entities\GeneratorForm */
$variableName = $viewGenerator->generatorForm->getResourceName(false, true);
?>
<h5 class="h3">
    <label><?= "{{ __('admin.columns.title') }}" ?></label>
    <span class="d-block"><?= "{{ $$variableName" . "->title }}" ?></span>
</h5>
