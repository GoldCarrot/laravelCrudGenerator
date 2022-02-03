<?php

namespace Chatway\LaravelCrudGenerator\Core\Templates\Routes;

use Chatway\LaravelCrudGenerator\Core\DTO\RouteParams;

/**
 * @property array                 $defaultTemplates
 * @property RouteParams []| array $templates
 */
class GeneratorRouteTemplates
{
    private array $defaultTemplates = [
        'admin' => [
            'template' => "<?php

Route::group(['middleware' => ['admin.auth']], function () {
    Route::resources([
        '{{resourceNamePlural}}' => '{{folderNs}}\{{resourceName}}Controller',
    ]);
});",
            'path'     => 'admin',
            'filename' => 'resource{{resourceName}}',
        ],
        'api'   => [
            'template' => "<?php

Route::group(['prefix' => '{{resourceTable}}', 'namespace' => '{{folderNs}}'], function () {
    Route::get('show', '{{resourceName}}Controller@show');
    Route::get('', '{{resourceName}}Controller@index');
});",
            'path'     => 'api',
            'filename' => '{{resourceTable}}',
        ],
    ];

    /**
     * @var RouteParams[]
     */
    private array $templates;

    public function __construct($templates = [])
    {
        foreach (array_replace($this->defaultTemplates, $templates) as $template) {
            $this->templates[] = new RouteParams($template);
        }
    }

    /**
     * @return array
     */
    public function getRoutes(): array
    {
        return $this->templates;
    }
}
