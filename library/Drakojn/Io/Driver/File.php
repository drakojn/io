<?php
namespace Drakojn\Io\Driver;

class File extends Stream
{
    /**
     * @param $resource
     *
     * @return bool
     */
    protected function validateResource($resource)
    {
        return file_exists($resource);
    }
}
