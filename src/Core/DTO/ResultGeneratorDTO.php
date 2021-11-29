<?php

namespace Chatway\LaravelCrudGenerator\Core\DTO;

class ResultGeneratorDTO
{
    public string $success;
    public string $fileName;
    public string $filePath;
    public string $modelNs;

    public function __construct($data)
    {
        $this->success = \Arr::get($data, 'success');
        $this->fileName = \Arr::get($data, 'fileName');
        $this->filePath = \Arr::get($data, 'filePath');
        $this->modelNs = \Arr::get($data, 'modelNs');
    }
}
