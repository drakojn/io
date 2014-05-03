<?php
namespace Drakojn\Io\Driver\Descriptor;

use Drakojn\Io\Mapper\Map;

class Ini extends AbstractDescriptor
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
        $data = $this->prepareData($map, $object);
        $content = [];
        foreach ($data as $localProperty => $value) {
            $value     = serialize($value);
            $content[] = "{$properties[$localProperty]}='{$value}'";
        }
        return implode(PHP_EOL, $content);
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
        $parsed          = parse_ini_string($data);
        $object = $this->injectDataIntoObject($map, $parsed);
        return $object;
    }
}