<?php
namespace Drakojn\Io\Driver\Descriptor;

use Drakojn\Io\Mapper\Map;

/**
 * Interface DescriptorInterface
 *
 * @package Drakojn\Io\Driver\Descriptor
 */
interface DescriptorInterface
{
    /**
     * Serializes Object
     *
     * @param Map   $map    the object structure map
     * @param mixed $object candidate to serialize
     *
     * @return mixed
     */
    public function serialize(Map $map, $object);

    /**
     * Unserializes data into an object
     *
     * @param Map    $map   the object structure map
     * @param string $data  serialized data
     * @param array  $query query to validate object unserialization
     *
     * @return mixed
     */
    public function unserialize(Map $map, $data, array $query = []);
} 