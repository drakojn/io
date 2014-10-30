<?php

namespace Drakojn\Io\Driver\SQL;

use PDO;
use ErrorException;
use Drakojn\Io\Mapper;
use ReflectionProperty;

/**
 * Insert SQL.
 *
 * @author Jefersson Nathan <malukenho@phpse.net>
 */
class Insert
{
    /**
     * Constructor.
     * 
     * @param PDO                $pdo
     * @param \Drakojn\Io\Mapper $mapper
     * @param Object             $object
     * @param array              $data
     * 
     * @return bool
     * 
     * @throws ErrorException
     */
    public function __construct(PDO $pdo, Mapper $mapper, $object, array $data)
    {
        $map        = $mapper->getMap();
        $properties = $map->getProperties();
        $identifier = $map->getIdentifier();
        
        $remoteIdentifier = $properties[$identifier];
        unset($properties[$identifier]);
        
        $insert = 'INSERT INTO ' . $map->getRemoteName();
        $columns = '(' . implode(', ', $properties) . ')';
        $values = 'VALUES (:' . implode(', :', array_keys($properties)) . ')';
        $sql = implode(' ', [$insert, $columns, $values]);
        
        $statement = $pdo->prepare($sql);
        
        if (!$statement) {
            throw new ErrorException('A SQL hasn\'t been generated: [' . $sql . ']');
        }
        
        unset($data[$identifier]);
        
        foreach ($data as $field => $value) {
            $statement->bindValue(':' . $field, $value);
        }
        
        $execution = $statement->execute();
        
        if ($execution) {
            $identity = $pdo->lastInsertId($remoteIdentifier);
            $reflection = new ReflectionProperty(get_class($object), $identifier);
            $reflection->setAccessible(true);
            $reflection->setValue($object, $identity);
        }
        
        return (bool) $execution;
    }
}
