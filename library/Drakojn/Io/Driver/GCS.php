<?php
namespace Drakojn\Io\Driver;

class GCS extends Stream
{
    /**
     * @param $resource
     *
     * @return bool
     */
    protected function validateResource($resource)
    {
        if (strpos($resource, 'gs://') === 0) {
            return true;
        }
    }
}