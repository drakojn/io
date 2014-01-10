<?php
namespace Drakojn\Io;

use Drakojn\Io\Mapper\Map;
use Drakojn\Io\AdapterInterface as Adapter;

abstract class Mapper
{
    protected $adapter;
    protected $map;

    public function __construct(Adapter $adapter, Map $map)
    {
        $this->adapter = $adapter;
        $this->map = $map;
    }

    /**
     * @return mixed
     */
    public function getAdapter()
    {
        return $this->adapter;
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
        return $this->adapter->find($this, $query);
    }

    public function findAll()
    {
        return $this->adapter->find($this, []);
    }

    public function findByIdentifier($identifierQuery)
    {
        $identifier = $this->map->getIdentifier();
        return $this->adapter->find($this, [$identifier => $identifierQuery]);
    }

    public function save($object)
    {
        $this->checkForTraitUsage($object);
        return $this->adapter->save($this, $object);
    }

    public function delete($object)
    {
        $this->checkForTraitUsage($object);
        return $this->adapter->delete($this, $object);
    }

    protected function checkForTraitUsage($object)
    {
        if(in_array("Drakojn\\Io\\Proxy\\DataTrait",class_uses($object))){
            return true;
        }
        throw new \InvalidArgumentException('Object does not use DataTrait');
        return false;
    }
}