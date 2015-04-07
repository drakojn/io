<?php

namespace Drakojn\Io\Driver\SQL;

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
     * @param string  $table      Table name
     * @param array   $data       Column and value as key and value of array
     * @param         $condition  Condition for update statement
     */
    public function __construct(PDO $pdo, Mapper $mapper, array $data, $condition)
    {
        $map = $mapper->getMap();
        $properties = $map->getProperties();
        $identifier = $map->getIdentifier();

        $remoteIdentifier = $properties[$identifier];

        unset($properties[$identifier]);

        $update = 'UPDATE ' . $mapper->getDriver();

        $fields = [];
        foreach ($properties as $value => $field) {
            $fields[] = "{$field} = :{$value}";
        }

        $set = 'SET ' . implode(', ', $fields);
        $where = "WHERE {$remoteIdentifier} = :{$identifier}";

        $sql = implode(' ', [$update, $set, $where]);

        $statement = $pdo->prepare($sql);

        if (!$statement) {
            throw new ErrorException(
            sprintf('A SQL hasn\'t been generated: [%s]', $sql)
            );
        }

        foreach ($data as $field => $value) {
            $statement->bindValue(':' . $field, $value);
        }

        return (bool) $statement->execute();
    }
}
