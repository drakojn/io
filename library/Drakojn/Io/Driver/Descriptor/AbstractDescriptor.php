<?php
namespace Drakojn\Io\Driver\Descriptor;

use Drakojn\Io\Mapper\Map;

abstract class AbstractDescriptor implements DescriptorInterface
{
    public function prepareData(Map $map, $object)
    {
        $identifier = $map->getIdentifier();
        $data       = $map->getData($object);
        if (!isset($data[$identifier]) || !$data[$identifier]) {
            $data[$identifier] = spl_object_hash($object);
            $reflection        = new \ReflectionProperty($map->getLocalName(), $identifier);
            $reflection->setAccessible(true);
            $reflection->setValue($object, $data[$identifier]);
        }
        return $data;
    }

    /**
     * Serializes Object
     *
     * @param Map    $map    the object structure map
     * @param object $object candidate to serialize
     *
     * @return mixed
     */
    abstract public function serialize(Map $map, $object);

    /**
     * Unserializes data into an object
     *
     * @param Map    $map  the object structure map
     * @param string $data serialized data
     *
     * @return object
     */
    abstract public function unserialize(Map $map, $data);

    public function injectDataIntoObject(Map $map, array $data)
    {
        $reflection      = new \ReflectionClass($map->getLocalName());
        $object          = $reflection->newInstance();
        $localProperties = $map->getProperties();
        foreach ($localProperties as $localProperty => $remoteProperty) {
            $value = unserialize($data[$remoteProperty]);
            $reflectedProperty = $reflection->getProperty($localProperty);
            $reflectedProperty->setAccessible(true);
            $reflectedProperty->setValue($object, $value);
        }
        return $object;
    }
}
