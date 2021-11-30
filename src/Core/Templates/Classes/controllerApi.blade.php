<?php
/**
 * This is the template for generating the controller class of a specified table.
 */

/* @var $generator \Chatway\LaravelCrudGenerator\Core\Generators\ControllerGenerator */
/* @var $generator->generatorForm->properties array list of properties (property => [type, name. comment]) */

echo "<?php\n";
?>

namespace {{ $generator->generatorForm->getNsByClassName($generator->controllerParams->controllerName) }};

use {{ $generator->controllerParams->baseClass }};
use {{ $generator->generatorForm->modelName }};
use {{ $generator->generatorForm->presenterName }};

/**
 * This is the controller class for table "{{ $generator->generatorForm->resourceTable }}".
 * Class {{ \class_namespace($generator->controllerParams->controllerName) }}
 *
 * @package {{ $generator->controllerParams->controllerName }}
*/

class {{ basename($generator->controllerParams->controllerName) }} extends {{ basename($generator->controllerParams->baseClass) }}
{
    public function __construct(private \{{$generator->generatorForm->repositoryName}} $repository, private \{{$generator->generatorForm->serviceName}} $service)
    {
    }

    public function index()
    {
        return response()->json(
            [
                'success' => true,
                'data' => collect($this->repository->getLast(null))->map(fn({{$generator->generatorForm->getResourceName(false, false)}} ${{$generator->generatorForm->getResourceName(false, true)}}) => (new {{ basename($generator->generatorForm->presenterName) }}(${{$generator->generatorForm->getResourceName(false, true)}}))->toArray()),
            ]
        );
    }
}
