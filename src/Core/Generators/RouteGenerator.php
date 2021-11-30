<?php

namespace Chatway\LaravelCrudGenerator\Core\Generators;

use Chatway\LaravelCrudGenerator\Core\Base\Interfaces\GeneratorInterface;
use Chatway\LaravelCrudGenerator\Core\DTO\RouteParams;
use Chatway\LaravelCrudGenerator\Core\Entities\GeneratorForm;
use Chatway\LaravelCrudGenerator\Core\Helpers\ConsoleHelper;
use File;
use View;

class RouteGenerator implements GeneratorInterface
{
    public function __construct(public GeneratorForm $generatorForm, private RouteParams $routeParams)
    {
    }

    public function generate()
    {
        $variables = [
            'resourceTable'      => $this->generatorForm->resourceTable,
            'resourceName'       => $this->generatorForm->resourceName,
            'resourceNamePlural' => $this->generatorForm->getResourceName(true, false),
            'folderNs'           => $this->generatorForm->folderNs,
        ];
        $renderedModel = $this->routeParams->template;
        $renderedModel = $this->replaceVariables($renderedModel, $variables);
        $filename = $this->replaceVariables($this->routeParams->filename . ".php", $variables);
        $path = $this->replaceVariables(base_path('routes/' . $this->routeParams->path), $variables);
        if (!File::isDirectory($path)) {
            File::makeDirectory($path, 0777, true, true);
        }

        if (!File::exists($path . '\\' . $filename) || $this->generatorForm->force) {
            File::delete($path . '\\' . $filename);
            if (File::put($path . '\\' . $filename, $renderedModel) !== false) {
                ConsoleHelper::info("Route $filename generated! Path in app: " . $path . '\\' . $filename);
            } else {
                ConsoleHelper::error("Service $filename generate error!");
            }
        } else {
            ConsoleHelper::warning("Service $filename is exists! Add --force option to overwrite Service!");
        }
    }

    private function replaceVariables($template, $variables)
    {
        foreach ($variables as $key => $value) {
            $template = str_replace("{{{$key}}}", $value, $template);
        }
        return $template;
    }
}
