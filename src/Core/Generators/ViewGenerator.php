<?php

namespace Chatway\LaravelCrudGenerator\Core\Generators;

use Chatway\LaravelCrudGenerator\Core\Base\Interfaces\GeneratorInterface;
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
        $namespace = $this->generatorForm->getEnumNs();
        $path = $this->generatorForm->mainPath . '/Core/Templates/Views';
        View::addLocation($path);
        View::addNamespace($this->viewName, $path);
        $renderedModel = View::make($this->viewName)->with(
            [
                'viewGenerator' => $this,
            ]);
        $filename = $this->viewName . $this->generatorForm::VIEW_FILE_SUFFIX;
        $path = resource_path($this->generatorForm->viewsPath);
        if (!File::isDirectory($path)) {
            File::makeDirectory($path, 0777, true, true);
        }
        if (!File::exists($path . '\\' . $filename) || $this->generatorForm->force) {
            File::delete($path . '\\' . $filename);
            if (File::put($path . '\\' . $filename, $renderedModel) !== false) {
                ConsoleHelper::info('View generated! Path in app: ' . lcfirst($namespace) . '\\' . $filename);
            } else {
                ConsoleHelper::error('View generate error!');
            }
        } else {
            ConsoleHelper::info('View is exists! Add --force option to overwrite View!');
        }
    }

    public function getBaseClassWithNs()
    {
        return $this->baseClassNs . '\\' . $this->baseClass;
    }

    public function getFormattedProperty($property)
    {
        return "\$model->{$property['name']} = Arr::get(\$data, '{$property['name']}', \$model->{$property['name']});";
    }

    public function renderedPropertyFormExist(PropertyDTO $propertyDTO)
    {
        $path = $this->generatorForm->mainPath . '/Core/Templates/Views/Form';
        view()->addLocation($path);
        view()->addNamespace($propertyDTO->name, $path);
        if ($propertyDTO->type == 'Carbon' || $propertyDTO->class) {
            return true;
        }
        return view()->exists($propertyDTO->name) || (!view()->exists($propertyDTO->name) && $propertyDTO->type == 'string');
    }

    public function getRenderedPropertyForm(PropertyDTO $propertyDTO)
    {
        $path = $this->generatorForm->mainPath . '/Core/Templates/Views/Form';
        view()->addLocation($path);
        if ($propertyDTO->name == 'status'){
            //dd($propertyDTO);
        }
        if ($propertyDTO->isEnum) {
            view()->addNamespace('selectEnum', $path);
            return view()->make('selectEnum')->with(
                [
                    'propertyDTO'   => $propertyDTO,
                    'viewGenerator' => $this,
                ]);
        }

        if ($propertyDTO->type == 'Carbon') {
            view()->addNamespace('dateFormat', $path);
            return view()->make('dateFormat')->with(
                [
                    'propertyDTO'   => $propertyDTO,
                    'viewGenerator' => $this,
                ]);
        }

        if ($propertyDTO->class) {
            if (class_basename($propertyDTO->class) == 'Image') {
                return view()->make('image')->with(
                    [
                        'propertyDTO'   => $propertyDTO,
                        'viewGenerator' => $this,
                    ]);
            }
            if (class_basename($propertyDTO->class) == 'File') {
                return view()->make('file')->with(
                    [
                        'propertyDTO'   => $propertyDTO,
                        'viewGenerator' => $this,
                    ]);
            }
            return view()->make('select')->with(
                [
                    'propertyDTO'   => $propertyDTO,
                    'viewGenerator' => $this,
                ]);
        }
        if (!view()->exists($propertyDTO->name) && $propertyDTO->type == 'string') {
            view()->addNamespace('string', $path);
            return view()->make('string')->with(
                [
                    'propertyDTO'   => $propertyDTO,
                    'viewGenerator' => $this,
                ]);
        }
        view()->addNamespace($propertyDTO->name, $path);
        return view()->make($propertyDTO->name)->with(
            [
                'propertyDTO'   => $propertyDTO,
                'viewGenerator' => $this,
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
        return view()->exists($propertyDTO->name) || (!view()->exists($propertyDTO->name) && $propertyDTO->type == 'string');
    }

    public function getRenderedPropertyShow(PropertyDTO $propertyDTO)
    {
        $path = $this->generatorForm->mainPath . '/Core/Templates/Views/Show';
        view()->addLocation($path);

        if ($propertyDTO->type == 'Carbon') {
            view()->addNamespace('dateFormat', $path);
            return view()->make('dateFormat')->with(
                [
                    'propertyDTO'   => $propertyDTO,
                    'viewGenerator' => $this,
                ]);
        }

        /*if ($propertyDTO->class) {
            if (class_basename($propertyDTO->class) == 'Image') {
                return view()->make('image')->with(
                    [
                        'propertyDTO'   => $propertyDTO,
                        'viewGenerator' => $this,
                    ]);
            }
            if (class_basename($propertyDTO->class) == 'File') {
                return view()->make('file')->with(
                    [
                        'propertyDTO'   => $propertyDTO,
                        'viewGenerator' => $this,
                    ]);
            }
            return view()->make('select')->with(
                [
                    'propertyDTO'   => $propertyDTO,
                    'viewGenerator' => $this,
                ]);
        }*/
        if (!view()->exists($propertyDTO->name) && $propertyDTO->type == 'string') {
            view()->addNamespace('string', $path);
            return view()->make('string')->with(
                [
                    'propertyDTO'   => $propertyDTO,
                    'viewGenerator' => $this,
                ]);
        }
        view()->addNamespace($propertyDTO->name, $path);
        return view()->make($propertyDTO->name)->with(
            [
                'propertyDTO'   => $propertyDTO,
                'viewGenerator' => $this,
            ]);
    }
}
