<?php

namespace Drakojn\Io\Driver\SQL;

use PDO;
use Drakojn\Io\Mapper;

/**
 * Delete SQL.
 *
 * @author Jefersson Nathan <malukenho@phpse.net>
 */
class Delete implements Action
{
    /**
     * Delete action.
     * 
     * @param PDO                $pdo
     * @param \Drakojn\Io\Mapper $mapper
     * @param Object             $object
     * 
     * @return PDOStatement
     */
    public function __construct(PDO $pdo, Mapper $mapper, $object)
    {
        $map              = $mapper->getMap();
        $identifier       = $map->getIdentifier();
        $remoteIdentifier = $map->getProperties()[$identifier];
        
        $data   = $map->getData($object);
        
        $delete    = 'DELETE FROM ' . $map->getRemoteName();
        $where     = 'WHERE ' . $remoteIdentifier . ' = :' . $identifier;
        $this->sql = "{$delete} {$where}";
        
        $statement = $pdo->prepare($this->sql);
        $statement->bindValue(':' . $identifier, $data[$identifier]);
        
        $execution = $statement->execute();
        
        return $execution;
    }
}
