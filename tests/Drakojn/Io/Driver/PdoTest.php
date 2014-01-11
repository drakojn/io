<?php
namespace Drakojn\Io\Driver;

use Drakojn\Io\Mapper;
use Pdo as PHPDataObject;

class PdoTest extends DriverTestAbstract
{
    protected $object;
    protected $pdo;

    public function setUp()
    {
        $this->object = new Pdo($this->buildDB());
        $this->mapper = $this->getMapper();
    }

    public function tearDown()
    {
        $this->object = null;
        $this->mapper = null;
        $this->pdo = null;
    }

    protected function buildDB()
    {
        $this->pdo = new PHPDataObject('sqlite::memory:');
        $this->pdo->query("
            CREATE TABLE IF NOT EXISTS user(
              id_user INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
              login VARCHAR(20) UNIQUE,
              name VARCHAR(50),
              email VARCHAR(50),
              password VARCHAR(50)
            );
        ");
        $data = $this->getDataStore();
        foreach($data as $each){
            $this->pdo->query("
                INSERT INTO user (login, name, email, password) VALUES ('{$each[0]}','{$each[1]}','{$each[2]}','{$each[3]}');
            ");
        }
        return $this->pdo;
    }

}