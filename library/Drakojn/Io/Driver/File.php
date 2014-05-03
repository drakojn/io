<?php
namespace Drakojn\Io\Driver;

use Drakojn\Io\DriverInterface;
use Drakojn\Io\Mapper;

class File extends Stream
{
    protected $resource;
    protected $reflection;

    public function _____construct($resource)
    {
        if(is_dir((string) $resource)){
            $this->resource = (string) $resource;
            return;
        }
        throw new \InvalidArgumentException('The argument '.$resource.' isn\'t a directory');
    }

    protected function buildPattern(array $properties = [])
    {
        $patternPart = [];
        foreach($properties as $local => $remote){
            $patternPart[] = "({$remote}=(?<{$local}>.*))";
        }
        return "/.*".implode('|',$patternPart).".*/";
    }

    protected function getSingleReflection(Mapper\Map $map)
    {
        if(!$this->reflection){
            $this->reflection = new \ReflectionClass($map->getLocalName());
        }
        return $this->reflection;
    }

    protected function buildObjectByFile($file, Mapper\Map $map, array $query = [])
    {
        $file = file_get_contents($file);
        $properties = $map->getProperties();
        $pattern = $this->buildPattern($properties);
        $matches = [];
        preg_match_all($pattern, $file, $matches);
        $matches = array_intersect_key($matches, $properties);
        $reflection = $this->getSingleReflection($map);
        $object = $reflection->newInstanceArgs([]);
        foreach($matches as $propertyName => $value){
            $value = current(array_filter($value,'strlen'));
            //TODO: refactor
            if(isset($query[$propertyName]) && $query[$propertyName] != $value){
                return null;
            }
            $property = $reflection->getProperty($propertyName);
            $property->setAccessible(true);
            $property->setValue($object, $value);
        }
        return $object;
    }

    public function save(Mapper $mapper, $object)
    {
        $map = $mapper->getMap();
        $properties = $map->getProperties();
        $identifier = $map->getIdentifier();
        $data = $map->getData($object);
        $new = false;
        if(!isset($data[$identifier]) || !$data[$identifier]){
            $data[$identifier] = spl_object_hash($object);
            $new = true;
        }
        $identity = $data[$identifier];
        $content = [];
        foreach($data as $localProperty => $value){
            $content[] = "{$properties[$localProperty]}={$value}";
        }
        $content = implode(PHP_EOL,$content);
        $fileName = "{$this->resource}".$map->getRemoteName()."/{$identity}";
        $return = (bool)file_put_contents($fileName,$content);
        if($return && $new){
            $reflection = new \ReflectionProperty(get_class($object), $identifier);
            $reflection->setAccessible(true);
            $reflection->setValue($object, $identity);
        }
        return $return;
    }

    public function delete(Mapper $mapper, $object)
    {
        $map = $mapper->getMap();
        $identifier = $map->getIdentifier();
        $data = $map->getData($object);
        $identity = $data[$identifier];
        $fileName = "{$this->resource}".$map->getRemoteName()."/{$identity}";
        $execution = true;
        if(file_exists($fileName)){
            $execution = unlink($fileName);
        }
        return $execution;
    }
}