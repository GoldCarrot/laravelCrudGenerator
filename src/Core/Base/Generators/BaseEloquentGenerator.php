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
        return strtolower(str_replace('Generator', '', basename(static::class)));
    }

    public function rollback()
    {
        if (File::exists($this->path . '\\' . $this->filename) && File::delete($this->path . '\\' . $this->filename)) {
            ConsoleHelper::info("$this->filename deleted! Path in app: " . $this->path . '\\');
        }
        if (File::isDirectory($this->path) && count(scandir($this->path)) <= 2) {
            if (File::deleteDirectory($this->path)) {
                ConsoleHelper::info("Path $this->path deleted!");
            }
        }

        //Удаление общей директории с условием, что она пустая
        $pathUp = dirname($this->path);
        if (File::isDirectory($pathUp) && count(scandir($pathUp)) <= 2) {
            if (File::deleteDirectory($pathUp)) {
                ConsoleHelper::info("Path $pathUp deleted!");
            }
        }
    }
}
