<?php
namespace Drakojn\Io\Driver;

use Drakojn\Io\DriverInterface;
use Drakojn\Io\Mapper;
use \Pdo as PHPDataObject;

class Pdo implements DriverInterface
{
    protected $resource;

    public function __construct(PHPDataObject $resource)
    {
        $this->resource = $resource;
    }

    public function find(Mapper $mapper, array $query = [])
    {
        $iterator = $this->findAndGetIterator($mapper, $query);
        return $iterator->fetchAll();
    }

    public function findAndGetIterator(Mapper $mapper, array $query = [])
    {
        $map = $mapper->getMap();
        $selectFields = [];
        foreach($map->getProperties() as $alias => $field){
            $selectFields[] = "{$field} as `{$alias}`";
        }
        $select = 'SELECT '.implode(', ',$selectFields);
        $from = 'FROM '.$map->getRemoteName();
        $whereParameters = [];
        $fields = array_keys($query);
        foreach($fields as $field){
            $whereParameters[] = "{$field} = :{$field}";
        }
        $where = '';
        if($whereParameters){
            $where = 'WHERE '.implode(' AND ', $whereParameters);
        }
        $sql = implode(' ',[$select, $from, $where]);
        $statement = $this->resource->prepare($sql);
        if(!$statement){
            throw new \ErrorException('A SQL hasn\'t been generated: ['.$sql.']');
        }
        $statement->setFetchMode(PHPDataObject::FETCH_CLASS, $map->getLocalName());
        foreach($query as $field => $value){
            $statement->bindValue($field, $value);
        }
        $ok = $statement->execute();
        return $statement;
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