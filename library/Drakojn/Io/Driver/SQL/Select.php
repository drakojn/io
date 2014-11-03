<?php

namespace Drakojn\Io\Driver\SQL;

use PDO;
use PDOStatement;
use ErrorException;
use Drakojn\Io\Mapper;

/**
 * Select SQL.
 *
 * @author Jefersson Nathan <malukenho@phpse.net>
 */
class Select
{
    /**
     * @var PDOStatement
     */
    private $pdoStatement;

    /**
     * @param PDO    $pdo
     * @param Mapper $mapper
     * @param array  $query
     *
     * @throws ErrorException
     */
    public function __construct(PDO $pdo, Mapper $mapper, array $query = [])
    {
        $map = $mapper->getMap();
        $selectFields = [];

        foreach ($map->getProperties() as $alias => $field) {
            $selectFields[] = "{$field} as `{$alias}`";
        }

        $select = 'SELECT ' . implode(', ', $selectFields);
        $from = 'FROM ' . $map->getRemoteName();

        $whereParameters = [];
        $fields = array_keys($query);
        foreach ($fields as $field) {
            $whereParameters[] = "{$field} = :{$field}";
        }

        $where = '';

        if ($whereParameters) {
            $where = 'WHERE ' . implode(' AND ', $whereParameters);
        }

        $sql = implode(' ', [$select, $from, $where]);
        $statement = $pdo->prepare($sql);

        if (!$statement) {
            throw new ErrorException('A SQL hasn\'t been generated: [' . $sql . ']');
        }

        $statement->setFetchMode(PDO::FETCH_CLASS, $map->getLocalName());

        foreach ($query as $field => $value) {
            $statement->bindValue(':' . $field, $value);
        }

        $statement->execute();

        $this->pdoStatement = $statement;
    }

    public function getStatement()
    {
        return $this->pdoStatement;
    }
}
