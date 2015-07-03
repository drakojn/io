<?php
namespace Drakojn\Io\Driver;

use Drakojn\Io\Mapper;
use \PDO as PHPDataObject;
use Drakojn\Io\DriverInterface;
use Drakojn\Io\Driver\SQL\Insert;
use Drakojn\Io\Driver\SQL\Select;
use Drakojn\Io\Driver\SQL\Update;
use Drakojn\Io\Driver\SQL\Delete;

/**
 * Class Pdo
 *
 * @package Drakojn\Io\Driver
 */
class Pdo implements DriverInterface
{
    /**
     * PDO
     *
     * @var PHPDataObject
     */
    protected $pdo;

    /**
     * Constructor.
     *
     * @param PHPDataObject $pdo a Pdo instance
     */
    public function __construct(PHPDataObject $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Searches for object matching $query
     *
     * @param Mapper $mapper the Mapper
     * @param array  $query  the Query
     *
     * @return array
     */
    public function find(Mapper $mapper, array $query = [])
    {
        $iterator = new Select($this->pdo, $mapper, $query);
        return $iterator->getStatement()->fetchAll();
    }

    /**
     * Persists $object
     *
     * @param Mapper $mapper Mapper
     * @param object $object Object to be saved
     *
     * @return Insert|Update
     */
    public function save(Mapper $mapper, $object)
    {
        $identifier = $mapper->getMap()->getIdentifier();
        $data       = $mapper->getMap()->getData($object);
        if ($data[$identifier]) {
            return $this->update($mapper, $data);
        }
        return $this->insert($mapper, $object, $data);
    }

    protected function insert(Mapper $mapper, $object, array $data)
    {
        $object = new Insert($this->pdo, $mapper, $object, $data);
        return $object();
    }

    protected function update(Mapper $mapper, array $data)
    {
        $object = new Update($this->pdo, $mapper, $data);
        return $object();
    }

    public function delete(Mapper $mapper, $object)
    {
        $object = new Delete($this->pdo, $mapper, $object);
        return $object();
    }
}
