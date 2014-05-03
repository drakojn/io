<?php
namespace Drakojn\Io\Driver;

abstract class AbstractDriver
{
    protected $resource;

    abstract protected function validateResource($resource);
}