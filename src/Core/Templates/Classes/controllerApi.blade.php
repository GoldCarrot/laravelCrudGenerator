<?php
/**
 * This is the template for generating the controller class of a specified table.
 */

/* @var $controllerGenerator \Chatway\LaravelCrudGenerator\Core\Generators\ControllerGenerator */
/* @var $controllerGenerator->generatorForm->properties array list of properties (property => [type, name. comment]) */

echo "<?php\n";
?>

namespace {{ $controllerGenerator->generatorForm->getNsByClassName($controllerGenerator->controllerParams->controllerName) }};

use {{ $controllerGenerator->controllerParams->baseClass }};
use {{ $controllerGenerator->generatorForm->modelName }};
use {{ $controllerGenerator->generatorForm->presenterName }};

/**
 * This is the controller class for table "{{ $controllerGenerator->generatorForm->resourceTable }}".
 * Class {{ \class_namespace($controllerGenerator->controllerParams->controllerName) }}
 *
 * @package {{ $controllerGenerator->controllerParams->controllerName }}
*/

class {{ basename($controllerGenerator->controllerParams->controllerName) }} extends {{ basename($controllerGenerator->controllerParams->baseClass) }}
{
    public function __construct(private \{{$controllerGenerator->generatorForm->repositoryName}} $repository, private \{{$controllerGenerator->generatorForm->serviceName}} $service)
    {
    }

    public function index()
    {
        return response()->json(
            [
                'success' => true,
                'data' => collect($this->repository->getLast(null))->map(fn({{$controllerGenerator->generatorForm->getResourceName(false, false)}} ${{$controllerGenerator->generatorForm->getResourceName(false, true)}}) => (new {{ basename($controllerGenerator->generatorForm->presenterName) }}(${{$controllerGenerator->generatorForm->getResourceName(false, true)}}))->toArray()),
            ]
        );
    }
}
