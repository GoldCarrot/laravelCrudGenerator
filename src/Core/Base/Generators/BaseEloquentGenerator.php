<?php

namespace Chatway\LaravelCrudGenerator\Core\Base\Generators;

use Chatway\LaravelCrudGenerator\Core\Helpers\ConsoleHelper;
use File;

abstract class BaseEloquentGenerator
{
    protected string $pathTemplate;
    protected string $path;
    protected string $filename;

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
}
