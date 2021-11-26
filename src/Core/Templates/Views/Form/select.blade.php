<?php
/**
 * This is the template for generating the service class of a specified table.
 */

/* @var $viewGenerator \Chatway\LaravelCrudGenerator\Core\Generators\ViewGenerator */
/* @var $propertyDTO \Chatway\LaravelCrudGenerator\Core\DTO\PropertyDTO */
$repository =
    str_replace($viewGenerator->generatorForm::MODEL_FOLDER_NAME, $viewGenerator->generatorForm::REPOSITORY_FOLDER_NAME, $propertyDTO->class);
$repository = '(new ' . $repository . $viewGenerator->generatorForm::REPOSITORY_SUFFIX . '())';
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
<?= "{{ BsForm::select('$propertyDTO->name')
                                ->value(old('$propertyDTO->name', {$repository}->getArrayForSelect()))
                                ->placeholder(__('admin.columns.{$propertyDTO->getNameForSelect()}'))
                                ->label(__('admin.columns.{$propertyDTO->getNameForSelect()}'))
                        }}" . PHP_EOL ?>
