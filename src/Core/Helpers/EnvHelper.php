<?php

namespace Chatway\LaravelCrudGenerator\Core\Helpers;

class EnvHelper
{
    public static function get($parameterName)
    {
        $pathPackage = dirname(__DIR__);
        $filePath = $pathPackage . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '.env';
        if (file_exists($filePath)) {
            $env = parse_ini_file($filePath);
            if (array_key_exists($parameterName, $env)) {
                return $env[$parameterName];
            } else {
                ConsoleHelper::error("Not '$parameterName' parameter in env");
            }
        }
        return null;
    }

    public static function devMode(): bool
    {
        return self::get('APP_ENV') == 'dev';
    }
}
