<?php

namespace Chatway\LaravelCrudGenerator\Core\DTO;

/**
 * @package  Chatway\LaravelCrudGenerator\Core\DTO\EnumParams
 */
class ControllerParams
{
    public mixed $templateName;
    public mixed $controllerName;
    public mixed $ns;
    public mixed $baseClass;

    public function __construct($data)
    {
        $this->templateName = \Arr::get($data, 'templateName', 'controller');
        $this->controllerName = \Arr::get($data, 'controllerName');
        $this->ns = \Arr::get($data, 'ns');
        $this->baseClass = \Arr::get($data, 'baseClass');
    }
}
