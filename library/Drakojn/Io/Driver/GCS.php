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
        return (in_array('gs',stream_get_wrappers()) && strpos($resource, 'gs://') === 0);
    }

    protected function buildUri($identifier)
    {
        return "{$this->resource}{$identifier}";
    }
}