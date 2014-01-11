<?php
namespace Drakojn\Io\Driver;

use Drakojn\Io\AdapterInterface;
use Drakojn\Io\Mapper;
use \Pdo as PHPDataObject;

class Pdo implements AdapterInterface
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
        foreach($map as $alias => $field){
            $selectFields[] = "{$field} as `{$alias}`";
        }
        $select = 'SELECT '.implode(', ',$selectFields);
        $from = 'FROM '.$map->getRemoteName();
        $whereParameters = [];
        $fields = array_keys($query);
        foreach($fields as $field){
            $whereParameters[] = "{$field} => :{$field}";
        }
        $where = '';
        if($whereParameters){
            $where = 'WHERE '.implode(' AND '.$whereParameters);
        }
        $sql = implode(' ',[$select, $from, $where]);
        $statement = $this->resource->query($sql);
        $statement->setFetchMode(PHPDataObject::FETCH_CLASS, $map->getLocalName());
        foreach($query as $field => $value){
            $statement->bindValue($field, $value);
        }
        $statement->execute();
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