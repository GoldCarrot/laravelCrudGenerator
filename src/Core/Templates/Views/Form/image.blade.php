<?php
/**
 * This is the template for generating the service class of a specified table.
 */
/* @var $generator \Chatway\LaravelCrudGenerator\Core\Generators\ViewGenerator */
/* @var $propertyDTO \Chatway\LaravelCrudGenerator\Core\DTO\PropertyDTO */
$propertyNameCamelCase = Str::camel($propertyDTO->name);
$resourceName = $generator->generatorForm->getResourceName(false, true);
$imageFieldName = Str::singular(str_replace('_id', '', $propertyDTO->classTable));
?>


<div class="col-lg-12">
    <h6 class="heading-small text-muted mb-4">Изображение</h6>
    <div class="pl-lg-4">
        <?= "{{ BsForm::text('$propertyNameCamelCase')
                                        ->value(old('$propertyNameCamelCase', \${$generator->generatorForm->getResourceName(false, true)}->$propertyDTO->name))
                                        ->placeholder(__('admin.columns.image'))
                                        ->attribute(['single-image-cropper'=> true, 'hidden' => true, 'preview' => find_image(old('$propertyNameCamelCase', \$$resourceName->".$imageFieldName."?->id))?->url])
                                        ->wrapperAttribute(['class' => 'user-avatar'])
                                }}" . PHP_EOL ?>
    </div>
</div>
