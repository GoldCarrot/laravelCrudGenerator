<?php

namespace Chatway\LaravelCrudGenerator\Core\Helpers;

use Illuminate\Console\Concerns\InteractsWithIO;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionException;
use RegexIterator;
use Symfony\Component\Console\Output\ConsoleOutput;

class ClassHelper
{
    use InteractsWithIO;
    /**
     * Description Запрос всех моделей в проекте
     * @param string $path
     * @return array
     * @throws ReflectionException
     */
    public static function getAllEntitiesInProject(string $path = "app\\Domain"): array
    {
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(base_path($path)));
        $regex = new RegexIterator($iterator, '/^.+\.php$/i', RegexIterator::GET_MATCH);
        $classes = [];
        foreach ($regex as $file => $value) {
            if (preg_match("/^.*Domain\\\.*\\\Entities\\\.*$/", $file)) {
                $className = str_replace(base_path("app"), 'App', $file);
                $className = str_replace('.php', '', $className);
                $classes[] = $className;
            }
        }
        return self::getTablesByClassArray($classes);
    }

    /**
     * Description получение всех таблиц в моделях
     * @param $classes
     * @return array
     * @throws ReflectionException
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
            }
        }
        return $tables;
    }
}
