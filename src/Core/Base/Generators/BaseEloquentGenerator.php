<?php

namespace Chatway\LaravelCrudGenerator\Core\Base\Generators;

use Chatway\LaravelCrudGenerator\Core\Entities\GeneratorForm;
use Chatway\LaravelCrudGenerator\Core\Helpers\ConsoleHelper;
use File;
use LogicException;
use View;

abstract class BaseEloquentGenerator
{
    protected string     $pathTemplate;
    protected string     $path;
    protected string     $filename;
    public GeneratorForm $generatorForm;

    public static function label(): string
    {
        return strtolower(str_replace('Generator', '', class_basename(static::class)));
    }

    public function rollback()
    {
        if (File::exists($this->getFilePath()) && File::delete($this->getFilePath())) {
            ConsoleHelper::info("{$this->getFilename()} deleted! Path in app: " . $this->getPath() . '/');
        }
        if (File::isDirectory($this->getPath()) && count(scandir($this->getPath())) <= 2) {
            if (File::deleteDirectory($this->getPath())) {
                ConsoleHelper::info("Path {$this->getPath()} deleted!");
            }
        }

        //Удаление общей директории с условием, что она пустая
        $pathUp = dirname($this->getPath());
        if (File::isDirectory($pathUp) && count(scandir($pathUp)) <= 2) {
            if (File::deleteDirectory($pathUp)) {
                ConsoleHelper::info("Path $pathUp deleted!");
            }
        }
    }

    protected function getPathTemplate(): string
    {
        return $this->pathTemplate;
    }

    protected function getPath(): string
    {
        return $this->path;
    }

    protected function getFilePath(): string
    {
        return "$this->path/$this->filename";
    }

    protected function getFilename(): string
    {
        return $this->filename;
    }

    protected function getTemplateFileName($folder, $templateName)
    {
        if (View::exists("generator.$folder." . $templateName)) {
            return "generator.$folder." . $templateName;
        } else {
            View::addLocation($this->getPathTemplate());
            View::addNamespace($templateName, $this->getPathTemplate());
            return $templateName;
        }
    }
    //todo сначала проверять генератор, с которого выполняется функция
    public function scenarioValue($name): string
    {
        //$this->getGenerator()
        foreach ($this->generatorForm->generators as $generator) {
            foreach ($generator->options as $key => $paramValue) {
                if ($key == $name && is_string($paramValue)) {
                    return $paramValue;
                } elseif (is_object($paramValue) && property_exists($paramValue, $name)) {
                    return $paramValue->$name;
                }
            }
        }
        throw new LogicException('Value is not found in Scenario: ' . $name);
    }
}
