<?php
namespace Drakojn\Io\Driver\Descriptor;

use Drakojn\Io\Mapper\Map;

class Json implements DescriptorInterface
{
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
        $properties = $map->getProperties();
        $identifier = $map->getIdentifier();
        $data       = $map->getData($object);
        if (!isset($data[$identifier]) || !$data[$identifier]) {
            $data[$identifier] = spl_object_hash($object);
        }
        $content = [];
        foreach ($data as $localProperty => $value) {
            $value     = serialize($value);
            $content[$properties[$localProperty]] = $value;
        }
        return json_encode((object) $content);
    }

    /**
     * Unserializes data into an object
     *
     * @param Map    $map   the object structure map
     * @param string $data  serialized data
     *
     * @return mixed
     */
    public function unserialize(Map $map, $data)
    {
        $parsed          = json_decode($data);
        $reflection      = new \ReflectionClass($map->getLocalName());
        $object          = $reflection->newInstance();
        $localProperties = $map->getProperties();
        foreach ($localProperties as $localProperty => $remoteProperty) {
            $value = unserialize($parsed[$remoteProperty]);
            if (isset($query[$localProperty]) && $query[$localProperty] != $value) {
                return null;
            }
            $reflectedProperty = $reflection->getProperty($localProperty);
            $reflectedProperty->setAccessible(true);
            $reflectedProperty->setValue($object, $value);
        }
        return $object;
    }
}