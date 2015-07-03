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
    protected $statement;

    /**
     * Delete action.
     *
     * @param PDO                $pdo
     * @param \Drakojn\Io\Mapper $mapper
     * @param Object             $object
     *
     * @return \Drakojn\Io\Driver\SQL\Delete
     */
    public function __construct(PDO $pdo, Mapper $mapper, $object)
    {
        $map              = $mapper->getMap();
        $identifier       = $map->getIdentifier();
        $remoteIdentifier = $map->getProperties()[$identifier];
        $data             = $map->getData($object);
        $delete           = 'DELETE FROM ' . $map->getRemoteName();
        $where            = 'WHERE ' . $remoteIdentifier . ' = :' . $identifier;
        $this->sql        = "{$delete} {$where}";
        $this->statement  = $pdo->prepare($this->sql);
        $this->statement->bindValue(':' . $identifier, $data[$identifier]);
    }

    public function __invoke()
    {
        return $this->statement->execute();
    }
}
