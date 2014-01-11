<?php
namespace Drakojn\Io;

use Drakojn\Io\Mapper\Map;
use Drakojn\Io\DriverInterface as Driver;

class Mapper
{
    protected $driver;
    protected $map;

    public function __construct(Driver $driver, Map $map)
    {
        $this->driver = $driver;
        $this->map = $map;
    }

    /**
     * @return mixed
     */
    public function getDriver()
    {
        return $this->driver;
    }

    /**
     * @return \Drakojn\Io\Mapper\Map
     */
    public function getMap()
    {
        return $this->map;
    }

    public function find(array $query = [])
    {
        return $this->driver->find($this, $query);
    }

    public function findAll()
    {
        return $this->driver->find($this, []);
    }

    public function findByIdentifier($identifierQuery)
    {
        $identifier = $this->map->getIdentifier();
        return $this->driver->find($this, [$identifier => $identifierQuery]);
    }

    public function save($object)
    {
        return $this->driver->save($this, $object);
    }

    public function delete($object)
    {
        return $this->driver->delete($this, $object);
    }

}