<?php
namespace Drakojn\Io\Driver;

use Drakojn\Io\Driver\Descriptor\DescriptorInterface;
use Drakojn\Io\Driver\Descriptor\Ini as Descriptor;
use Drakojn\Io\DriverInterface;
use Drakojn\Io\Mapper;

/**
 * Class Stream
 *
 * @package Drakojn\Io\Driver
 */
abstract class Stream implements DriverInterface
{
    /**
     * @var string
     */
    protected $wrapper;
    /**
     * @var
     */
    protected $context;
    /**
     * @var Descriptor\DescriptorInterface
     */
    protected $descriptor;

    /**
     * @param mixed               $resource
     * @param DescriptorInterface $descriptor
     */
    public function __construct($resource, DescriptorInterface $descriptor = null)
    {
        $this->validateResource($resource);
        $this->wrapper    = 'file';
        $wrapperCandidate = strtok($resource, '://');
        if ($wrapperCandidate) {
            $this->wrapper = $wrapperCandidate;
        }
        $this->resource   = $resource;
        $this->descriptor = $this->descriptor ? : new Descriptor;
    }

    /**
     * @param $resource
     *
     * @return bool
     */
    abstract protected function validateResource($resource);

    /**
     * @param Descriptor\DescriptorInterface $descriptor The Descriptor
     */
    public function setDescriptor($descriptor)
    {
        $this->descriptor = $descriptor;
    }

    /**
     * @return \Drakojn\Io\Driver\Descriptor\DescriptorInterface
     */
    public function getDescriptor()
    {
        return $this->descriptor;
    }

    /**
     * @param $identifier
     *
     * @return string
     */
    protected function buildUri($identifier)
    {
        return realpath("{$this->resource}/{$identifier}");
    }

    /**
     * @param Mapper\Map $map
     * @param            $identifier
     *
     * @return mixed
     */
    protected function read(Mapper\Map $map, $identifier)
    {
        $url = $this->buildUri($map->getRemoteName() . '/' . $identifier);
        if (!file_exists($url) || !is_file($url)) {
            return;
        }
        $data = file_get_contents($url, false, $this->context ? : null);
        return $this->descriptor->unserialize($map, $data);
    }

    /**
     * @param Mapper\Map $map
     * @param            $data
     *
     * @return bool
     */
    protected function write(Mapper\Map $map, $data)
    {
        $reflectionProperty = new \ReflectionProperty($map->getLocalName(), $map->getIdentifier());
        $reflectionProperty->setAccessible(true);
        $identifier = $reflectionProperty->getValue($data);
        $uri        = $this->buildUri($map->getRemoteName() . '/' . $identifier);
        $new        = false;
        if (!$identifier) {
            $identifier = spl_object_hash($data);
            $uri        = $this->buildUri($map->getRemoteName()) . '/' . $identifier;
            $new        = true;
        }
        $result = (bool)file_put_contents(
            $uri,
            $this->descriptor->serialize($map, $data),
            false,
            $this->context ? : null
        );
        if ($result && $new) {
            $reflectionProperty->setValue($data, $identifier);
        }
        return $result;
    }

    /**
     * @param Mapper $mapper
     * @param array  $query
     *
     * @return array
     */
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

    /**
     * @param Mapper $mapper
     * @param mixed  $object
     *
     * @return bool
     */
    public function save(Mapper $mapper, $object)
    {
        return $this->write($mapper->getMap(), $object);
    }

    /**
     * @param Mapper $mapper
     * @param mixed  $object
     *
     * @return bool
     */
    public function delete(Mapper $mapper, $object)
    {
        $map                = $mapper->getMap();
        $reflectionProperty = new \ReflectionProperty($map->getLocalName(), $map->getIdentifier());
        $reflectionProperty->setAccessible(true);
        $identifier = $reflectionProperty->getValue($object);
        $uri        = $this->buildUri($map->getRemoteName() . '/' . $identifier);
        if (!file_exists($uri)) {
            return true;
        }
        if ($this->context) {
            return unlink($uri, $this->context);
        }
        return unlink($uri);
    }
}