<?php

namespace Drakojn\Io\Driver;

use Drakojn\Io\Mapper;
use \PDO as PHPDataObject;
use Drakojn\Io\DriverInterface;
use Drakojn\Io\Driver\SQL\Insert;
use Drakojn\Io\Driver\SQL\Select;
use Drakojn\Io\Driver\SQL\Update;
use Drakojn\Io\Driver\SQL\Delete;

class Pdo implements DriverInterface
{
    /**
     *
     * @var PHPDataObject
     */
    protected $pdo;

    /**
     * Constructor.
     * 
     * @param PHPDataObject $pdo
     */
    public function __construct(PHPDataObject $pdo)
    {
        $this->pdo = $pdo;
    }

    public function find(Mapper $mapper, array $query = [])
    {
        $iterator = new Select($this->pdo, $mapper, $query);
        return $iterator->getStatement()->fetchAll();
    }

    public function save(Mapper $mapper, $object)
    {
        $identifier = $mapper->getMap()->getIdentifier();
        $data       = $mapper->getMap()->getData($object);
        
        if ($data[$identifier]) {
            return $this->update($mapper, $object, $data);
        }
        
        return $this->insert($mapper, $object, $data);
    }

    protected function insert(Mapper $mapper, $object, array $data)
    {
        return new Insert($this->pdo, $mapper, $object, $data);
    }

    protected function update(Mapper $mapper, array $data)
    {
        return new Update($this->pdo, $mapper, $data, $condition);
    }
    
    public function delete(Mapper $mapper, $object)
    {
        return new Delete($this->pdo, $mapper, $object);
    }
}
