<?php
namespace Drakojn\Io\Driver\Descriptor;

use Drakojn\Io\Mapper\Map;

class Ini implements DescriptorInterface
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
        $properties = $map->getProperties();
        $identifier = $map->getIdentifier();
        $data       = $map->getData($object);
        if (!isset($data[$identifier]) || !$data[$identifier]) {
            $data[$identifier] = spl_object_hash($object);
        }
        $content = [];
        foreach ($data as $localProperty => $value) {
            $value     = serialize($value);
            $content[] = "{$properties[$localProperty]}=\"{$value}\"";
        }
        return implode(PHP_EOL, $content);
    }

    /**
     * Unserializes data into an object
     *
     * @param Map    $map   the object structure map
     * @param string $data  serialized data
     * @param array  $query query to validate object unserialization
     *
     * @return mixed
     */
    public function unserialize(Map $map, $data, array $query = [])
    {
        $parsed     = parse_ini_string($data);
        $reflection = new \ReflectionClass($map->getLocalName());
        $object     = $reflection->newInstance();
        $localProperties = $map->getProperties();
        foreach ($localProperties as $localProperty => $remoteProperty) {
            $value             = unserialize($parsed[$remoteProperty]);
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