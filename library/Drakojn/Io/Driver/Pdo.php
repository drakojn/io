<?php
namespace Drakojn\Io\Driver;

use Drakojn\Io\Driver\SQL\Update;
use Drakojn\Io\DriverInterface;
use Drakojn\Io\Mapper;
use ErrorException;
use \PDO as PHPDataObject;

class Pdo implements DriverInterface
{
    /**
     *
     * @var PHPDataObject
     */
    protected $pdo;

    public function __construct(PHPDataObject $pdo)
    {
        $this->pdo = $pdo;
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
        $statement = $this->pdo->prepare($sql);
        if(!$statement){
            throw new ErrorException('A SQL hasn\'t been generated: ['.$sql.']');
        }
        $statement->setFetchMode(PHPDataObject::FETCH_CLASS, $map->getLocalName());
        foreach($query as $field => $value){
            $statement->bindValue(':'.$field, $value);
        }
        $statement->execute();

        return $statement;
    }

    public function save(Mapper $mapper, $object)
    {
        $identifier = $mapper->getMap()->getIdentifier();
        $data = $mapper->getMap()->getData($object);
        if($data[$identifier]){
            return $this->update($mapper, $object, $data);
        }
        return $this->insert($mapper, $object, $data);
    }

    protected function insert(Mapper $mapper, $object, array $data)
    {
        $map = $mapper->getMap();
        $properties = $map->getProperties();
        $identifier = $map->getIdentifier();
        $remoteIdentifier = $properties[$identifier];
        unset($properties[$identifier]);
        $insert = 'INSERT INTO ' . $map->getRemoteName();
        $columns = '('.implode(', ',$properties).')';
        $values = 'VALUES (:'.implode(', :',array_keys($properties)).')';
        $sql = implode(' ', [$insert, $columns, $values]);
        $statement = $this->pdo->prepare($sql);
        if(!$statement){
            throw new ErrorException('A SQL hasn\'t been generated: ['.$sql.']');
        }
        unset($data[$identifier]);
        foreach($data as $field => $value){
            $statement->bindValue(':'.$field, $value);
        }
        $execution = $statement->execute();
        if($execution){
            $identity = $this->pdo->lastInsertId($remoteIdentifier);
            $reflection = new \ReflectionProperty(get_class($object), $identifier);
            $reflection->setAccessible(true);
            $reflection->setValue($object, $identity);
        }
        return (bool) $execution;
    }

    protected function update(Mapper $mapper, array $data)
    {
        $map        = $mapper->getMap();
        $properties = $map->getProperties();
        $identifier = $map->getIdentifier();

        $remoteIdentifier = $properties[$identifier];

        unset($properties[$identifier]);

        $update = new Update(
            $map->getRemoteName(),
            $properties,
            "{$remoteIdentifier} = :{$identifier}"
        );

        $statement = $this->pdo->prepare($update->getQuery());

        if (! $statement) {
            throw new ErrorException(
                sprintf('A SQL hasn\'t been generated: [%s]', $update->getQuery())
            );
        }

        foreach($data as $field => $value){
            $statement->bindValue(':'.$field, $value);
        }

        return (bool) $statement->execute();
    }

    public function delete(Mapper $mapper, $object)
    {
        $map = $mapper->getMap();
        $identifier = $map->getIdentifier();
        $remoteIdentifier = $map->getProperties()[$identifier];
        $data = $map->getData($object);
        $delete = 'DELETE FROM '.$map->getRemoteName();
        $where = 'WHERE '.$remoteIdentifier.' = :'.$identifier;
        $sql = "{$delete} {$where}";
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(':'.$identifier, $data[$identifier]);
        $execution = $statement->execute();
        return $execution;
    }
}
