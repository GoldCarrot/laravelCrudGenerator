<?php

namespace Chatway\LaravelCrudGenerator\Core\Services;

use Chatway\LaravelCrudGenerator\Core\Helpers\DB\ForeignKeyService;
use Chatway\LaravelCrudGenerator\Core\Entities\GeneratorForm;

class GeneratorFormService
{
    public function getGeneratorForm($data)
    {
        $generatorForm = new GeneratorForm(new ForeignKeyService());
        $generatorForm->setResourceTable(\Arr::get($data, 'resourceTable'));
        $generatorForm->resourceName = \Arr::get($data, 'resourceName');
        $generatorForm->baseNs = \Arr::get($data, 'baseNs');
        $generatorForm->httpNs = \Arr::get($data, 'httpNs');
        return $generatorForm;
    }
}
