<?php
/**
 * This is the template for generating the service class of a specified table.
 */

/* @var $generator \Chatway\LaravelCrudGenerator\Core\Generators\ViewGenerator */
/* @var $propertyDTO \Chatway\LaravelCrudGenerator\Core\DTO\PropertyDTO */
$repository =
    str_replace($generator->generatorForm::$MODEL_FOLDER_NAME, $generator->generatorForm::$REPOSITORY_FOLDER_NAME, $propertyDTO->class);
$variableName = Str::pluralStudly( lcfirst(class_basename($propertyDTO->class)));
$repository = '(new ' . $repository . $generator->generatorForm::$REPOSITORY_SUFFIX . '())';
$propertyNameCamelCase = Str::camel($propertyDTO->name)
///////////Пример функции getArrayForSelect
///**
// * @return Collection
// */
//public function getArrayForSelect(): Collection
//{
//    return $this->query()->select('id', 'title')->get()->mapWithKeys(function ($item) {
//        return [$item['id'] => $item['title']];
//    });
//}
?>
<?= "{{ BsForm::select('$propertyNameCamelCase', $$variableName)
                                ->value(old('$propertyNameCamelCase', \${$generator->generatorForm->getResourceName(false, true)}->$propertyDTO->name))
                                ->placeholder(__('admin.columns.$propertyNameCamelCase'))
                                ->label(__('admin.columns.$propertyNameCamelCase'))
                        }}" . PHP_EOL ?>
