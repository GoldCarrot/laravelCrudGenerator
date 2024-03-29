<?php

namespace Chatway\LaravelCrudGenerator\Core\Generators;

use Chatway\LaravelCrudGenerator\Core\Base\Generators\BaseEloquentGenerator;
use Chatway\LaravelCrudGenerator\Core\Base\Interfaces\GeneratorInterface;
use Chatway\LaravelCrudGenerator\Core\DTO\EnumParams;
use Chatway\LaravelCrudGenerator\Core\DTO\PropertyDTO;
use Chatway\LaravelCrudGenerator\Core\Entities\GeneratorForm;
use Chatway\LaravelCrudGenerator\Core\Helpers\ConsoleHelper;
use File;
use Illuminate\Contracts\Container\BindingResolutionException;
use View;

/**
 * @property string $viewName
 */
class ViewGenerator extends BaseEloquentGenerator implements GeneratorInterface
{
    public mixed $viewName;

    public function __construct(public GeneratorForm $generatorForm, $options)
    {
        $this->viewName = \Arr::get($options, 'viewName');
        $this->pathTemplate = $this->generatorForm->mainPath . '/Core/Templates/Views';
        $this->filename = $this->viewName . $this->generatorForm::$VIEW_FILE_SUFFIX;
        $this->path = resource_path(str_replace('\\', '/', \Arr::get($options, 'viewsPath')));
    }

    public function generate()
    {
        $templateName = $this->getTemplateFileName('classes', $this->viewName);

        if (!File::isDirectory($this->getPath())) {
            File::makeDirectory($this->getPath(), 0777, true, true);
        }
        if (!File::exists($this->getFilePath()) || $this->generatorForm->force) {
            $renderedModel = View::make($templateName)->with(
                [
                    'generator' => $this,
                ]);
            File::delete($this->getFilePath());
            if (File::put($this->getFilePath(), $renderedModel) !== false) {
                ConsoleHelper::info("{$this->getFileName()} generated! Path in app: " . $this->getPath() . '/');
            } else {
                ConsoleHelper::error("{$this->getFileName()} generate error!");
            }
        } else {
            ConsoleHelper::warning("{$this->getFileName()} is exists! Add --force option to overwrite View!");
        }
    }

    public function renderedPropertyFormExist(PropertyDTO $propertyDTO): bool
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
        $this->pathTemplate = $path;
        if ($propertyDTO->isEnum) {
            return view()->make($this->getTemplateFileName('views.form', 'selectEnum'))->with(
                [
                    'propertyDTO' => $propertyDTO,
                    'generator'   => $this,
                ]);
        }
        if ($propertyDTO->type == 'Carbon') {
            return view()->make($this->getTemplateFileName('views.form', 'dateFormat'))->with(
                [
                    'propertyDTO' => $propertyDTO,
                    'generator'   => $this,
                ]);
        }

        if ($propertyDTO->class) {
            if (class_basename($propertyDTO->class) == 'Image') {
                return view()->make($this->getTemplateFileName('views.form', 'image'))->with(
                    [
                        'propertyDTO' => $propertyDTO,
                        'generator'   => $this,
                    ]);
            }
            if (class_basename($propertyDTO->class) == 'File') {
                return view()->make($this->getTemplateFileName('views.form', 'file'))->with(
                    [
                        'propertyDTO' => $propertyDTO,
                        'generator'   => $this,
                    ]);
            }
            /**
             * Если не Image и не File, то выбор элемента один к одному
             */
            return view()->make($this->getTemplateFileName('views.form', 'select'))->with(
                [
                    'propertyDTO' => $propertyDTO,
                    'generator'   => $this,
                ]);
        }
        /** Если нет предустановленных шаблонов, то генерируем обычный string input **/
        if (!view()->exists($propertyDTO->name)) {
            return view()->make($this->getTemplateFileName('views.form', 'string'))->with(
                [
                    'propertyDTO' => $propertyDTO,
                    'generator'   => $this,
                ]);
        }
        /** Здесь можно создавать собственные шаблоны в папке views/generator/views/form/ **/
        return view()->make($this->getTemplateFileName('views.form', $propertyDTO->name))->with(
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
        return view()->exists($propertyDTO->name) || (!view()->exists($propertyDTO->name));
    }

    /**
     * @throws BindingResolutionException
     */
    public function getRenderedPropertyShow(PropertyDTO $propertyDTO)
    {
        $path = $this->generatorForm->mainPath . '/Core/Templates/Views/Show';
        $this->pathTemplate = $path;

        if ($propertyDTO->type == 'Carbon') {
            return view()->make($this->getTemplateFileName('views.show', 'dateFormatShow'))->with(
                [
                    'propertyDTO' => $propertyDTO,
                    'generator'   => $this,
                ]);
        }

        if ($propertyDTO->class) {
            if (class_basename($propertyDTO->class) == 'Image') {
                return view()->make($this->getTemplateFileName('views.show', 'imageShow'))->with(
                    [
                        'propertyDTO' => $propertyDTO,
                        'generator'   => $this,
                    ]);
            }
        }
        /** Если нет предустановленных шаблонов, то генерируем обычный <span></span>**/
        if (!view()->exists($propertyDTO->name . 'Show')) {
            return view()->make($this->getTemplateFileName('views.show', 'stringShow'))->with(
                [
                    'propertyDTO' => $propertyDTO,
                    'generator'   => $this,
                ]);
        }
        /** Здесь можно создавать собственные шаблоны в папке views/generator/views/show/ **/
        return view()->make($this->getTemplateFileName('views.show', $propertyDTO->name . 'Show'))->with(
            [
                'propertyDTO' => $propertyDTO,
                'generator'   => $this,
            ]);
    }

    /**
     * Description генерирует столбцы на странице Index
     * Шаблоны распространненых столбцов внутри функции
     * Пока нет возможности извне взять шаблоны
     *
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
            'modelName'          => $this->scenarioValue('modelName'),
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
                        'value' => function (\{{modelName}} \${{variableName}}) {
                            return \{{statusName}}::label(\${{variableName}}->status);
                        },
                        'filter' => [
                            'class' => \Itstructure\GridView\Filters\DropdownFilter::class,
                            'data' => \$statuses
                        ]
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
