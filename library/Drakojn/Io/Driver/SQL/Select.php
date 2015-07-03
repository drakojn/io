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
     * The statement
     * @var PDOStatement
     */
    protected $pdoStatement;

    /**
     * Select's constructor
     *
     * @param PDO    $pdo    Pdo instance
     * @param Mapper $mapper the Mapper
     * @param array  $query  a query on '[key=>value,key=>value,..]' style
     *
     * @throws ErrorException
     */
    public function __construct(PDO $pdo, Mapper $mapper, array $query = [])
    {
        $map          = $mapper->getMap();
        $selectFields = [];
        $properties   = $map->getProperties();
        foreach ($properties as $alias => $field) {
            $selectFields[] = "{$field} as `{$alias}`";
        }
        $select          = 'SELECT ' . implode(', ', $selectFields);
        $from            = 'FROM ' . $map->getRemoteName();
        $whereParameters = [];
        $queryParameters = array_keys($query);
        foreach ($queryParameters as $key) {
            $whereParameters[] = "{$properties[$key]} = :{$key}";
        }
        $where = '';
        if ($whereParameters) {
            $where = 'WHERE ' . implode(' AND ', $whereParameters);
        }
        $sql       = implode(' ', [$select, $from, $where]);
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

    /**
     * Retrieves the statement
     *
     * @return PDOStatement
     */
    public function getStatement()
    {
        return $this->pdoStatement;
    }
}
