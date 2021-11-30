<?php
/**
 * This is the template for generating the service class of a specified table.
 */
/* @var $generator \Chatway\LaravelCrudGenerator\Core\Entities\GeneratorForm */
/* @var $propertyDTO \Chatway\LaravelCrudGenerator\Core\DTO\PropertyDTO */

//в классе File должен быть объявлен метод
//public function getInfoForDZAttribute()
//{
//    return json_encode(['title' => $this->name, 'size' => $this->size, 'id' => $this->id]);
//}
?>
                    <div class="col-lg-12 mb-5">
                            <h6 class="heading-small text-muted mb-4">Файл</h6>
                            <div class="pl-lg-4">
                                <?= "{{ Form::file('$propertyDTO->name', [
                                        'dropzone' => true,
                                        'hidden' => true,
                                        'data-files' => '[' . (\${$generator->generatorForm->getResourceName(false, true)}->".Str::singular(str_replace('_id', '', $propertyDTO->classTable))."->infoForDZ ?? '') . ']',
                                ]) }}" . PHP_EOL ?>
                            </div>
                        </div>
