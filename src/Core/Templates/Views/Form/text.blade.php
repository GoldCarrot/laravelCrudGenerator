<?php
/**
 * This is the template for generating the service class of a specified table.
 */
/* @var $generator \Chatway\LaravelCrudGenerator\Core\Entities\GeneratorForm */
/* @var $propertyDTO \Chatway\LaravelCrudGenerator\Core\DTO\PropertyDTO */
?>
                    <div class="col-lg-12">
                            <h6 class="heading-small text-muted mb-4">Текст</h6>
                            <div class="pl-lg-4">
                                <?= "{{ BsForm::text('$propertyDTO->name')
                                        ->value(old('$propertyDTO->name', \${$generator->generatorForm->getResourceName(false, true)}->$propertyDTO->name))
                                        ->attribute(['editor' => true, 'hidden' => true])
                                }}" . PHP_EOL ?>
                            </div>
                        </div>
