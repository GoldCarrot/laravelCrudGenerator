<?php

namespace Chatway\LaravelCrudGenerator\Core\Generators;

use Arr;
use Chatway\LaravelCrudGenerator\Core\Base\Generators\BaseEloquentGenerator;
use Chatway\LaravelCrudGenerator\Core\Base\Interfaces\GeneratorInterface;
use Chatway\LaravelCrudGenerator\Core\DTO\RouteParams;
use Chatway\LaravelCrudGenerator\Core\Entities\GeneratorForm;
use Chatway\LaravelCrudGenerator\Core\Helpers\ConsoleHelper;
use File;

class RouteGenerator extends BaseEloquentGenerator implements GeneratorInterface
{
    private array $variables;
    private RouteParams $routeParams;
    public function __construct(public GeneratorForm $generatorForm, public $options)
    {
        $this->routeParams = Arr::get($this->options, 'routeParams');
        $this->variables = [
            'resourceTable'      => str_replace('_', '-', $this->generatorForm->resourceTable),
            'resourceName'       => $this->generatorForm->resourceName,
            'resourceNamePlural' => $this->generatorForm->getResourceName(true, true, true),
            'resourceNameNotPlural' => $this->generatorForm->getResourceName(false, true, true),
            'folderNs'           => $this->generatorForm->folderNs,
        ];
        $this->pathTemplate = $this->generatorForm->mainPath . '/Core/Templates/Classes';
        $this->filename = $this->replaceVariables($this->routeParams->filename . ".php", $this->variables);
        $this->path = $this->replaceVariables(str_replace('\\', '/', base_path('routes' . (strlen($this->routeParams->path) ? '/' : '') . $this->routeParams->path)), $this->variables);
    }

    public function generate()
    {

        if (!File::isDirectory($this->getPath())) {
            File::makeDirectory($this->getPath(), 0777, true, true);
        }

        if (!File::exists($this->getFilePath()) || $this->generatorForm->force) {
            $renderedModel = $this->replaceVariables($this->routeParams->template, $this->variables);
            File::delete($this->getFilePath());
            if (File::put($this->getFilePath(), $renderedModel) !== false) {
                ConsoleHelper::info("{$this->getFileName()} generated! Path in app: " . $this->getFilePath());
            } else {
                ConsoleHelper::error("{$this->getFileName()} generate error!");
            }
        } else {
            ConsoleHelper::warning("{$this->getFileName()} is exists! Add --force option to overwrite Route!");
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
