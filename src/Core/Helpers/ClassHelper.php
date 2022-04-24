<?php

namespace Chatway\LaravelCrudGenerator\Core\Helpers;

use Illuminate\Console\Concerns\InteractsWithIO;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionException;
use RegexIterator;

class ClassHelper
{
    use InteractsWithIO;
    /**
     * Description Запрос всех моделей в проекте
     * @param string $path
     * @return array
     */
    public static function getAllEntitiesInProject(string $path = "app/Domain"): array
    {
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
        $regex = new RegexIterator($iterator, '/^.+\.php$/i', RegexIterator::GET_MATCH);
        $classes = [];
        foreach ($regex as $file => $value) {
            if (preg_match("/^.*Domain\/.*\/Entities\/.*$/", $file)) {
                $className = str_replace("app", 'App', $file);
                $className = str_replace('.php', '', $className);
                $className = str_replace('/', '\\', $className);
                $classes[] = $className;
            }
        }
        return self::getTablesByClassArray($classes);
    }

    /**
     * Description получение всех таблиц в моделях
     * @param $classes
     * @return array
     */
    public static function getTablesByClassArray($classes): array
    {
        $tables = [];
        foreach ($classes as $class) {
            try {
                $ref = new \ReflectionClass($class);
                if ($ref->isSubclassOf(Model::class)) {
                    $tables[] = [
                        'tableName' => $ref->getProperty('table')->getDefaultValue() ??
                                       Str::snake(Str::pluralStudly(class_basename($class))),
                        'className' => $class,
                    ];
                }
            } catch (ReflectionException $e) {
                info($e->getMessage());
                ConsoleHelper::error($e->getMessage());
            }
        }
        return $tables;
    }
    /**
     * Description Запрос всех ресурсов в проекте
     * @param string $path
     * @return array
     */
    public static function getAllResourcesInProject(string $path = "app/Http"): array
    {
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
        $regex = new RegexIterator($iterator, '/^.+\.php$/i', RegexIterator::GET_MATCH);
        $classes = [];
        foreach ($regex as $file => $value) {
            if (preg_match("/^.*Http\/.*\/Resources\/.*$/", $file)) {
                $className = str_replace("app", 'App', $file);
                $className = str_replace('.php', '', $className);
                $className = str_replace('/', '\\', $className);
                $classes[] = $className;
            }
        }
        return $classes;
    }

    public static function getResourceByName($name)
    {
        $resources = self::getAllResourcesInProject();
        foreach ($resources as $resource) {
            if (str_contains($resource, $name)) {
                return $resource;
            }
        }
        return $name;
    }
}
