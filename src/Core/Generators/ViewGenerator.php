<?php

namespace Chatway\LaravelCrudGenerator\Core\Generators;

use Chatway\LaravelCrudGenerator\Core\Base\Interfaces\GeneratorInterface;
use Chatway\LaravelCrudGenerator\Core\DTO\EnumParams;
use Chatway\LaravelCrudGenerator\Core\DTO\PropertyDTO;
use Chatway\LaravelCrudGenerator\Core\Entities\GeneratorForm;
use Chatway\LaravelCrudGenerator\Core\Helpers\ConsoleHelper;
use File;
use View;

/**
 * @property string $viewName
 */
class ViewGenerator implements GeneratorInterface
{
    public mixed $viewName;

    public function __construct(public GeneratorForm $generatorForm, $config = [])
    {
        $this->viewName = \Arr::get($config, 'viewName');
    }

    public function generate()
    {
        $path = $this->generatorForm->mainPath . '/Core/Templates/Views';
        View::addLocation($path);
        View::addNamespace($this->viewName, $path);
        $renderedModel = View::make($this->viewName)->with(
            [
                'generator' => $this,
            ]);
        $filename = $this->viewName . $this->generatorForm::$VIEW_FILE_SUFFIX;
        $path = resource_path($this->generatorForm->viewsPath);
        if (!File::isDirectory($path)) {
            File::makeDirectory($path, 0777, true, true);
        }
        if (!File::exists($path . '\\' . $filename) || $this->generatorForm->force) {
            File::delete($path . '\\' . $filename);
            if (File::put($path . '\\' . $filename, $renderedModel) !== false) {
                ConsoleHelper::info("View $this->viewName generated! Path in app: " . $path . '\\' . $filename);
            } else {
                ConsoleHelper::error("View $this->viewName generate error!");
            }
        } else {
            ConsoleHelper::warning("View $this->viewName is exists! Add --force option to overwrite View!");
        }
    }

    public function renderedPropertyFormExist(PropertyDTO $propertyDTO)
    {
        $path = $this->generatorForm->mainPath . '/Core/Templates/Views/Form';
        view()->addLocation($path);
        view()->addNamespace($propertyDTO->name, $path);
        if ($propertyDTO->type == 'Carbon' || $propertyDTO->class) {
            return true;
        }
        return view()->exists($propertyDTO->name) || (!view()->exists($propertyDTO->name));
    }

    public function getRenderedPropertyForm(PropertyDTO $propertyDTO)
    {
        $path = $this->generatorForm->mainPath . '/Core/Templates/Views/Form';
        view()->addLocation($path);
        if ($propertyDTO->isEnum) {
            view()->addNamespace('selectEnum', $path);
            return view()->make('selectEnum')->with(
                [
                    'propertyDTO' => $propertyDTO,
                    'generator'   => $this,
                ]);
        }

        if ($propertyDTO->type == 'Carbon') {
            view()->addNamespace('dateFormat', $path);
            return view()->make('dateFormat')->with(
                [
                    'propertyDTO' => $propertyDTO,
                    'generator'   => $this,
                ]);
        }

        if ($propertyDTO->class) {
            if (class_basename($propertyDTO->class) == 'Image') {
                return view()->make('image')->with(
                    [
                        'propertyDTO' => $propertyDTO,
                        'generator'   => $this,
                    ]);
            }
            if (class_basename($propertyDTO->class) == 'File') {
                return view()->make('file')->with(
                    [
                        'propertyDTO' => $propertyDTO,
                        'generator'   => $this,
                    ]);
            }
            /**
             * Если не Image и не File, то выбор элемента один к одному
             */
            return view()->make('select')->with(
                [
                    'propertyDTO' => $propertyDTO,
                    'generator'   => $this,
                ]);
        }
        if (!view()->exists($propertyDTO->name)) {
            view()->addNamespace('string', $path);
            return view()->make('string')->with(
                [
                    'propertyDTO' => $propertyDTO,
                    'generator'   => $this,
                ]);
        }
        view()->addNamespace($propertyDTO->name, $path);
        return view()->make($propertyDTO->name)->with(
            [
                'propertyDTO' => $propertyDTO,
                'generator'   => $this,
            ]);
    }

    public function renderedPropertyShowExist(PropertyDTO $propertyDTO)
    {
        $path = $this->generatorForm->mainPath . '/Core/Templates/Views/Show';
        view()->addLocation($path);
        view()->addNamespace($propertyDTO->name, $path);
        //if ($propertyDTO->type == 'Carbon'/* || $propertyDTO->class*/) {
        //    return true;
        //}
        return view()->exists($propertyDTO->name) || (!view()->exists($propertyDTO->name));
    }

    public function getRenderedPropertyShow(PropertyDTO $propertyDTO)
    {
        $path = $this->generatorForm->mainPath . '/Core/Templates/Views/Show';
        view()->addLocation($path);

        if ($propertyDTO->type == 'Carbon') {
            view()->addNamespace('dateFormat', $path);
            return view()->make('dateFormatShow')->with(
                [
                    'propertyDTO' => $propertyDTO,
                    'generator'   => $this,
                ]);
        }

        if ($propertyDTO->class) {
            if (class_basename($propertyDTO->class) == 'Image') {
                return view()->make('imageShow')->with(
                    [
                        'propertyDTO' => $propertyDTO,
                        'generator'   => $this,
                    ]);
            }/*
            if (class_basename($propertyDTO->class) == 'File') {
                return view()->make('file')->with(
                    [
                        'propertyDTO'   => $propertyDTO,
                        'generator' => $this,
                    ]);
            }
            return view()->make('select')->with(
                [
                    'propertyDTO'   => $propertyDTO,
                    'generator' => $this,
                ]);*/
        }
        /** Если нет предустановленных шаблонов, то генерируем обычный <span></span>**/
        if (!view()->exists($propertyDTO->name . 'Show')) {
            return view()->make('stringShow')->with(
                [
                    'propertyDTO' => $propertyDTO,
                    'generator'   => $this,
                ]);
        }
        view()->addNamespace($propertyDTO->name . 'Show', $path);
        return view()->make($propertyDTO->name . 'Show')->with(
            [
                'propertyDTO' => $propertyDTO,
                'generator'   => $this,
            ]);
    }

    /**
     * Description генерирует столбцы на странице Index
     * Шаблоны распространненых столбцов внутри функции
     * Пока нет возможности извне взять шаблоны
     * @return string
     */
    public function getIndexColumns(): string
    {
        $enumStatus = collect($this->generatorForm->enums)->first(function (EnumParams $enumParams) {
            return $enumParams->name == 'status';
        });
        $variables = [
            'variableName'       => $this->generatorForm->getResourceName(false, true),
            'resourceTable'      => $this->generatorForm->resourceTable,
            'resourceName'       => $this->generatorForm->resourceName,
            'modelName'          => $this->generatorForm->modelName,
            'resourceNamePlural' => $this->generatorForm->getResourceName(true, true),
            'folderNs'           => $this->generatorForm->folderNs,
        ];
        if ($enumStatus) {
            $variables['statusName'] = $enumStatus->enumName;
        }
        $result = "";
        $statusTemplate = ",
                    [
                        'label' => __('admin.columns.status'),
                        'attribute' => 'status',
                        'value' => function ({{modelName}} \${{resourceTable}}) {
                            return {{statusName}}::label(\${{resourceTable}}->status);
                        },
                    ]";
        $publishedAtTemplate = ",
                    [
                        'label' => __('admin.columns.published_at'),
                        'attribute' => 'published_at',
                        'value' => function ({{modelName}} \${{resourceTable}}) {
                            return \${{resourceTable}}->published_at->format('H:i d.m.Y');
                        },
                    ]";
        foreach ($this->generatorForm->properties as $property) {
            switch ($property->name) {
                case 'status':
                    $result .= $this->replaceVariables($statusTemplate, $variables);
                    break;
                case 'published_at':
                    $result .= $this->replaceVariables($publishedAtTemplate, $variables);
                    break;
            }
        }
        return $result;
    }

    private function replaceVariables($template, $variables)
    {
        foreach ($variables as $key => $value) {
            $template = str_replace("{{{$key}}}", $value, $template);
        }
        return $template;
    }
}
