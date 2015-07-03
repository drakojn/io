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
     * @var \PDOStatement
     */
    protected $statement;
    protected $map;
    protected $pdo;
    protected $object;

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
        unset($properties[$identifier]);
        $insert    = 'INSERT INTO ' . $map->getRemoteName();
        $columns   = '(' . implode(', ', $properties) . ')';
        $values    = 'VALUES (:' . implode(', :', array_keys($properties)) . ')';
        $sql       = implode(' ', [$insert, $columns, $values]);
        $statement = $pdo->prepare($sql);
        if (!$statement) {
            throw new ErrorException('A SQL hasn\'t been generated: [' . $sql . ']');
        }
        unset($data[$identifier]);
        foreach ($data as $field => $value) {
            $statement->bindValue(':' . $field, $value);
        }
        $this->statement = $statement;
        $this->map       = $map;
        $this->pdo       = $pdo;
        $this->object    = $object;
    }

    /**
     * @return bool
     */
    public function __invoke()
    {
        $identifier       = $this->map->getIdentifier();
        $properties       = $this->map->getProperties();
        $remoteIdentifier = $properties[$identifier];
        $execution        = $this->statement->execute();
        if ($execution) {
            $identity   = $this->pdo->lastInsertId($remoteIdentifier);
            $reflection = new ReflectionProperty(
                $this->map->getLocalName(),
                $identifier
            );
            $reflection->setAccessible(true);
            $reflection->setValue($this->object, $identity);
        }
        return (bool)$execution;
    }
}
