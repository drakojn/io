<?php
namespace Drakojn\Io;

use Drakojn\Io\Mapper\Map;

trait DataTrait
{
    public function getDataArray(Map $map)
    {
        $reflection = new \ReflectionObject($this);
        $dataArray = [];
        foreach(array_keys($map->getProperties()) as $localProperty){
            $property = $reflection->getProperty($localProperty);
            $property->setAccessible(true);
            $dataArray[$localProperty] = $property->getValue($this);
        }
        return $dataArray;
    }
}