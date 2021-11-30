<?php

namespace Chatway\LaravelCrudGenerator\Core\Templates\Routes;

class GeneratorRouteTemplates
{
    public $templates = [
        'admin' => [
            'template' => "<?php
Route::resources([
    '{{resourceNamePlural}}' => '{{folderNs}}\{{resourceName}}Controller',
]);",
            'path'     => 'admin',
            'filename' => 'resource{{resourceName}}',
        ],
        'api'   => [
            'template' => "<?php
Route::group(['prefix' => '{{resourceTable}}', 'namespace' => '{{folderNs}}'], function () {
    Route::get('show', '{{resourceName}}Controller@show');
    Route::get('index', '{{resourceName}}Controller@index');
});",
            'path'     => 'api',
            'filename' => '{{resourceTable}}',
        ],
    ];
    public function __construct($templates = [])
    {
        $this->templates = array_replace($this->templates, $templates);
    }

    public function getRoutes()
    {
        return $this->templates;
    }
}
