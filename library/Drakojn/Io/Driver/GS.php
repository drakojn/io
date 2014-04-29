<?php
namespace Drakojn\Io\Driver;

use Drakojn\Io\Mapper;

class GS extends File
{
    public function find(Mapper $mapper, array $query = [])
    {
        $map = $mapper->getMap();
        $pathMap = "{$this->resource}" . $map->getRemoteName() . "/";
        $store = [];
        $handler = opendir($pathMap);
        if (!$handler) {
            return [];
        }
        while (false !== ($file = readdir($handler))) {
            $store[] = $this->buildObjectByFile("{$pathMap}{$file}", $map, $query);
        }
        $result = array_values(array_filter($store));
        return $result;
    }

}
