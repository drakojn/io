<?php
namespace Drakojn\Io\Driver\SQL;

use ErrorException;
use PDO;
use \Drakojn\Io\Mapper;

/**
 * Update SQL.
 *
 * @author Jefersson Nathan <malukenho@phpse.net>
 */
class Update implements Action
{
    /**
     * Pdo Statement
     *
     * @var \PDOStatement
     */
    protected $statement;

    /**
     * Update's constructor
     *
     * @param PDO    $pdo    a Pdo instance
     * @param Mapper $mapper a Mapper instance
     * @param array  $data   data to persisted
     *
     * @throws ErrorException
     */
    public function __construct(PDO $pdo, Mapper $mapper, array $data)
    {
        $map        = $mapper->getMap();
        $properties = $map->getProperties();
        $identifier = $map->getIdentifier();
        $remoteIdentifier = $properties[$identifier];
        unset($properties[$identifier]);
        $update = 'UPDATE ' . $map->getRemoteName();
        $fields = [];
        foreach ($properties as $value => $field) {
            $fields[] = "{$field} = :{$value}";
        }
        $set   = 'SET ' . implode(', ', $fields);
        $where = "WHERE {$remoteIdentifier} = :{$identifier}";
        $sql = implode(' ', [$update, $set, $where]);
        $this->statement = $pdo->prepare($sql);
        if (!$this->statement) {
            throw new ErrorException(
                sprintf('A SQL hasn\'t been generated: [%s]', $sql)
            );
        }
        foreach ($data as $field => $value) {
            $this->statement->bindValue(':' . $field, $value);
        }
    }

    /**
     * Executes the statement
     * @return bool
     */
    public function __invoke()
    {
        return (bool)$this->statement->execute();
    }
}
