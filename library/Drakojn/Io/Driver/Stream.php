<?php
namespace Drakojn\Io\Driver;

use Drakojn\Io\Driver\Descriptor\DescriptorInterface;
use Drakojn\Io\Driver\Descriptor\Ini;
use Drakojn\Io\DriverInterface;
use Drakojn\Io\Mapper;

class Stream extends AbstractDriver implements DriverInterface
{
    protected $wrapper;
    protected $context;
    protected $descriptor;

    public function __construct($resource, DescriptorInterface $descriptor = null)
    {
        $this->validateResource($resource);
        $this->wrapper    = 'file';
        $wrapperCandidate = strtok($resource, '://');
        if ($wrapperCandidate) {
            $this->wrapper = $wrapperCandidate;
        }
        $this->resource   = $resource;
        $this->descriptor = $this->descriptor ? : new Ini;
    }

    protected function validateResource($resource)
    {
        return file_exists($resource);
    }

    protected function buildUri($identifier)
    {
        return realpath("{$this->resource}/{$identifier}");
    }

    protected function read(Mapper\Map $map, $identifier)
    {
        $url = $this->buildUri($map->getRemoteName() . '/' . $identifier);
        if (!file_exists($url) || !is_file($url)) {
            return;
        }
        $data = file_get_contents($url, false, $this->context ? : null);
        return $this->descriptor->unserialize($map, $data);
    }

    protected function write(Mapper\Map $map, $data)
    {
        $reflectionProperty = new \ReflectionProperty($map->getLocalName(), $map->getIdentifier());
        $reflectionProperty->setAccessible(true);
        $identifier = $reflectionProperty->getValue($data);
        $url        = $this->buildUri($map->getRemoteName() . '/' . $identifier);
        return (bool)file_put_contents(
            $url,
            $this->descriptor->serialize($map, $data),
            false,
            $this->context ? : null
        );
    }

    public function find(Mapper $mapper, array $query = [])
    {
        $map        = $mapper->getMap();
        $pathMap    = realpath($this->buildUri($map->getRemoteName()));
        $pathHandle = opendir($pathMap);
        $objects    = [];
        while (false !== ($file = readdir($pathHandle))) {
            $objects[] = $this->read($map, $file);
        }
        closedir($pathHandle);
        $objects = array_filter($objects);
        $objects = array_filter(
            $objects,
            function ($object) use ($map, $query) {
                $data   = $map->getData($object);
                $result = 1;
                foreach ($data as $property => $value) {
                    $result -= (isset($query[$property]) && $query[$property] != $value);
                }
                return (bool)$result;
            }
        );
        return array_values($objects);
    }

    public function save(Mapper $mapper, $object)
    {
        // TODO: Implement save() method.
    }

    public function delete(Mapper $mapper, $object)
    {
        // TODO: Implement delete() method.
    }
}