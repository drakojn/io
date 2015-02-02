<?php
namespace Drakojn\Io\Driver\Descriptor;

use Drakojn\Io\Mapper\Map;

class Json extends AbstractDescriptor
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
        $data       = $this->prepareData($map, $object);
        $content    = [];
        foreach ($data as $localProperty => $value) {
            $value                                = serialize($value);
            $content[$properties[$localProperty]] = $value;
        }
        return json_encode((object)$content);
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
        $parsed = json_decode($data, true);
        return $this->injectDataIntoObject($map, $parsed);
    }
}
