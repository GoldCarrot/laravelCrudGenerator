<?php

namespace Chatway\LaravelCrudGenerator\Core\Entities;
use Chatway\LaravelCrudGenerator\Core\Helpers\DB\ForeignKeyService;

/**
 * @property string $baseNs
 * @property string $httpNs
 * @property string $resourceName Названия моделей
 * @property string $resourceTable Таблица в бд
 * @property bool $carbonIsset
 * @property array $properties
 * @property array $columns
 */
class ModelForm extends GeneratorForm
{
    public $columns;

    public function __construct($data)
    {
        parent::__construct(new ForeignKeyService());
        $this->setResourceTable(\Arr::get($data, 'resourceTable'));
        $this->resourceName = \Arr::get($data, 'resourceName');
        $this->baseNs = \Arr::get($data, 'baseNs');
        $this->httpNs = \Arr::get($data, 'httpNs');
    }

    public function getFullName()
    {
        return $this->baseNs . 'Entities\\' . $this->resourceName;
    }

    public function getModelNs()
    {
        return $this->baseNs . 'Entities';
    }

    public function getFormattedProperties()
    {
        $maxLength = 0;
        foreach (array_merge($this->properties, $this->internalForeignKeys, $this->extrernalForeignKeys) as $property) {
            if ($maxLength < strlen($property['type'])) {
                $maxLength = strlen($property['type']);
            }
        }
        foreach ($this->properties as $index => $property) {
            $this->properties[$index]['formattedString'] = $property['type'] . str_repeat(' ', $maxLength - strlen($property['type'])). ' $' . $property['name'];
        }
        return $this->properties;
    }

}
