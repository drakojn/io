<?php
namespace Drakojn\Io\Driver;

use Drakojn\Io\DriverInterface;
use Drakojn\Io\Mapper;

class File implements DriverInterface
{
    protected $resource;

    public function __construct($resource)
    {
        if(is_dir((string) $resource)){
            $this->resource = (string) $resource;
            return;
        }
        throw new \InvalidArgumentException('The argument '.$resource.' isn\'t a directory');
    }

    protected function buildObjectByFile($file, Mapper\Map $map, array $query = [])
    {
        //test
        $file = file_get_contents($file);
        $properties = $map->getProperties();
        $patternPart = [];
        ///^.*((id_user=(?<id>.*)\n)|(login=(?<alias>.*)\n)).*$/
        foreach($properties as $local => $remote){
            $patternPart[] = "({$remote}=(?<{$local}>.*))";
        }
        $pattern = "/.*".implode('|',$patternPart).".*/";
        $matches = [];
        //test
        preg_match_all($pattern, $file, $matches);
        $matches = array_intersect_key($matches, $properties);
        $reflection = new \ReflectionClass($map->getLocalName());
        $object = $reflection->newInstanceArgs([]);
        foreach($matches as $propertyName => $value){
            $value = current(array_filter($value,'strlen'));
            //refactor
            if(isset($query[$propertyName]) && $query[$propertyName] != $value){
                return null;
            }
            $property = $reflection->getProperty($propertyName);
            $property->setAccessible(true);
            $property->setValue($object, $value);
        }
        return $object;
    }


    public function find(Mapper $mapper, array $query = [])
    {
        $map = $mapper->getMap();
        $pathMap = "{$this->resource}".$map->getRemoteName()."/";
        $store = [];
        foreach(glob($pathMap.'*') as $file){
            $store[] = $this->buildObjectByFile($file, $map, $query);
        }
        $result = array_values(array_filter($store));
        return $result;
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