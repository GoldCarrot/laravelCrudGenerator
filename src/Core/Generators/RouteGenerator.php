<?php

namespace Chatway\LaravelCrudGenerator\Core\Generators;

use Chatway\LaravelCrudGenerator\Core\Base\Generators\BaseEloquentGenerator;
use Chatway\LaravelCrudGenerator\Core\Base\Interfaces\GeneratorInterface;
use Chatway\LaravelCrudGenerator\Core\DTO\RouteParams;
use Chatway\LaravelCrudGenerator\Core\Entities\GeneratorForm;
use Chatway\LaravelCrudGenerator\Core\Helpers\ConsoleHelper;
use File;

class RouteGenerator extends BaseEloquentGenerator implements GeneratorInterface
{
    private array $variables;

    public function __construct(public GeneratorForm $generatorForm, private RouteParams $routeParams)
    {
        $this->variables = [
            'resourceTable'      => str_replace('_', '-', $this->generatorForm->resourceTable),
            'resourceName'       => $this->generatorForm->resourceName,
            'resourceNamePlural' => $this->generatorForm->getResourceName(true, true),
            'folderNs'           => $this->generatorForm->folderNs,
        ];

        $this->pathTemplate = $this->generatorForm->mainPath . '/Core/Templates/Classes';
        $this->filename = $this->replaceVariables($this->routeParams->filename . ".php", $this->variables);
        $this->path = $this->replaceVariables(base_path('routes/' . $this->routeParams->path), $this->variables);
    }

    public function generate()
    {
        $renderedModel = $this->replaceVariables($this->routeParams->template, $this->variables);
        if (!File::isDirectory($this->path)) {
            File::makeDirectory($this->path, 0777, true, true);
        }

        if (!File::exists($this->path . '\\' . $this->filename) || $this->generatorForm->force) {
            File::delete($this->path . '\\' . $this->filename);
            if (File::put($this->path . '\\' . $this->filename, $renderedModel) !== false) {
                ConsoleHelper::info("$this->filename generated! Path in app: " . $this->path . '\\' . $this->filename);
            } else {
                ConsoleHelper::error("$this->filename generate error!");
            }
        } else {
            ConsoleHelper::warning("$this->filename is exists! Add --force option to overwrite Route!");
        }
    }

    private function replaceVariables($template, $variables)
    {
        foreach ($variables as $key => $value) {
            $template = str_replace("{{" . $key . "}}", $value, $template);
        }
        return $template;
    }
}
