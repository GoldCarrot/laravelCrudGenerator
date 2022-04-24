<?php
/**
 * This is the template for generating the service class of a specified table.
 */
/* @var $generator \Chatway\LaravelCrudGenerator\Core\Entities\GeneratorForm */
/* @var $propertyDTO \Chatway\LaravelCrudGenerator\Core\DTO\PropertyDTO */
$propertyNameCamelCase = Str::camel($propertyDTO->name)
?>
                    <div class="col-lg-12">
                            <h6 class="heading-small text-muted mb-4">Изображение</h6>
                            <div class="pl-lg-4">
                                <?= '@php'. PHP_EOL ?>
                                    $preview = $<?=$generator->generatorForm->getResourceName(false, true)?>-><?=Str::singular(str_replace('_id', '', $propertyDTO->classTable))?>->url ?? null;
                                    if (($oldPreview = old('<?="{$propertyDTO->name}"?>', -1)) && $oldPreview !== -1) {
                                        $preview = find_image($oldPreview)->url ?? null;
                                    }
                                <?= '@endphp'. PHP_EOL ?>
                                <?= "{{ BsForm::text('$propertyNameCamelCase')
                                        ->value(old('$propertyNameCamelCase', \${$generator->generatorForm->getResourceName(false, true)}->$propertyDTO->name))
                                        ->placeholder(__('admin.columns.image'))
                                        ->attribute(['single-image-cropper'=> true, 'hidden' => true, 'preview' => \$preview])
                                        ->wrapperAttribute(['class' => 'user-avatar'])
                                }}" . PHP_EOL ?>
                            </div>
                        </div>
