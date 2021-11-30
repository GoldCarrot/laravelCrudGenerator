<?php
/**
 * This is the template for generating the
 */
/* @var $generator \Chatway\LaravelCrudGenerator\Core\Generators\ViewGenerator */
$variableName = $generator->generatorForm->getResourceName(false, true);
?>
<div class="mt-3">
                            <label>{{ __('admin.columns.image') }}</label><br>
                            <img src="<?= "{!! \${$variableName}->image->url ?? 'noImage.jpg' !!}"?>" width="400"/>
                        </div>
