<?php
namespace Drakojn\Io\Driver\Descriptor;

use Drakojn\Io\Mapper\Map;

/**
 * Class Php
 *
 * @package Drakojn\Io\Driver\Descriptor
 */
class Php implements DescriptorInterface
{
    protected static $reflections = [];

    /**
     * Serializes Object
     *
     * @param Map   $map    the object structure map
     * @param mixed $object candidate to serialize
     *
     * @return mixed
     */
    public function serialize(Map $map, $object)
    {
        $identifier = $map->getIdentifier();
        $data       = $map->getData($object);
        if (!isset($data[$identifier]) || !$data[$identifier]) {
            $id         = spl_object_hash($object);
            $reflection = new \ReflectionProperty($map->getLocalName(), $identifier);
            $reflection->setAccessible(true);
            $reflection->setValue($object, $id);
        }
        return serialize($object);
    }

    /**
     * Unserializes data into an object
     *
     * @param Map    $map  the object structure map
     * @param string $data serialized data
     *
     * @return mixed
     */
    public function unserialize(Map $map, $data)
    {
        return unserialize($data);
    }
}