<?php

namespace Chatway\LaravelCrudGenerator\Core\DTO;

/**
 * string $success
 * string $fileName
 * string $filePath
 * string $modelNs
 */
class ResultGeneratorDTO
{
    public $success;
    public $fileName;
    public $filePath;
    public $modelNs;

    public function __construct($data)
    {
        $this->success = \Arr::get($data, 'success');
        $this->fileName = \Arr::get($data, 'fileName');
        $this->filePath = \Arr::get($data, 'filePath');
        $this->modelNs = \Arr::get($data, 'modelNs');
    }
}
