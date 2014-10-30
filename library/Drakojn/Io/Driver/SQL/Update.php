<?php
namespace Drakojn\Io\Driver\SQL;

/**
 * Create Update statement.
 *
 * @author Jefersson Nathan <malukenho@phpse.net>
 */
class Update
{
    /**
     * Store sql query as a plain text
     *
     * @var string
     */
    private $sql;

    /**
     * @param string  $table      Table name
     * @param array   $data       Column and value as key and value of array
     * @param         $condition  Condition for update statement
     */
    public function __construct($table, array $data, $condition)
    {
        $update = 'UPDATE ' . $table;
        $fields = [];
        foreach($data as $value => $field){
            $fields[] = "{$field} = :{$value}";
        }
        $set   = 'SET '.implode(', ',$fields);
        $where = "WHERE {$condition}";

        $this->sql = implode(' ', [$update, $set, $where]);

        return $this;
    }

    /**
     * Sql query as text.
     *
     * @return string
     */
    public function getQuery()
    {
        return $this->sql;
    }
}
