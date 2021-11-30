<?php

namespace Chatway\LaravelCrudGenerator\Core\DTO;

/**
 * @package  Chatway\LaravelCrudGenerator\Core\DTO\RouteParams
 */
class RouteParams
{
    public string $template;
    public string $path;
    public string $filename;

    public function __construct($data)
    {
        $this->template = \Arr::get($data, 'template');
        $this->path = \Arr::get($data, 'path');
        $this->filename = \Arr::get($data, 'filename');
    }
}
