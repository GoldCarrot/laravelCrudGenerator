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
use {{ $generator->scenarioValue('modelName') }};
use {{ $generator->scenarioValue('resourceClassName') }};
use {{ $generator->scenarioValue('repositoryName') }};
use {{ $generator->scenarioValue('serviceName') }};
use Illuminate\Http\JsonResponse;

/**
 * This is the controller class for table "{{ $generator->generatorForm->resourceTable }}".
 * Class {{ \class_namespace($generator->controllerParams->controllerName) }}
 *
 * @package {{ $generator->controllerParams->controllerName }}
*/

class {{ class_basename($generator->controllerParams->controllerName) }} extends {{ class_basename($generator->controllerParams->baseClass) }}
{
    public function __construct(
        private {{ class_basename($generator->scenarioValue('repositoryName')) }} $repository,
        private {{ class_basename($generator->scenarioValue('serviceName')) }} $service,
    )
    {
    }

    public function index(): JsonResponse
    {
        return response()->json(
            [
                'success' => true,
                'data' => {{ class_basename($generator->scenarioValue('resourceClassName')) }}::collection($this->repository->allActive()),
            ]
        );
    }

    public function show({{$generator->generatorForm->getResourceName()}} ${{$generator->generatorForm->getResourceName(false, true)}}): JsonResponse
    {
        return response()->json(
            [
                'success' => true,
                'data' => {{ class_basename($generator->scenarioValue('resourceClassName')) }}::make(${{$generator->generatorForm->getResourceName(false, true)}}),
            ]
        );
    }
}
