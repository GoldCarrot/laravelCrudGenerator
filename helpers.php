<?php

if (! function_exists('class_namespace')) {
    /**
     * Get the class "namespace" of the given object / class.
     *
     * @param object|string $class
     *
     * @return string
     */
    function class_namespace(object|string $class): string
    {
        $class = is_object($class) ? get_class($class) : $class;
        return join("\\", array_slice(explode("\\", $class), 0, -1));
    }
}
