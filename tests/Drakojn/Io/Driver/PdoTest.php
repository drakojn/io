<?php
namespace Drakojn\Io\Driver;

use Drakojn\Io\Mapper;
use \Pdo as PHPDataObject;

class PdoTest extends \PHPUnit_Framework_TestCase
{
    protected $object;
    protected $mapper;

    public function setUp()
    {
        $this->object = new Pdo($this->buildDB());
        $this->mapper = $this->getMapper();
    }

    protected function buildDB()
    {
        $pdo = new PHPDataObject('sqlite::memory:');
        $pdo->query("
            CREATE TABLE IF NOT EXISTS user(
              id_user INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
              login VARCHAR(20) UNIQUE,
              name VARCHAR(50),
              email VARCHAR(50),
              password VARCHAR(50)
            );
        ");
        $data = [
            ['duodraco', 'Anderson Casimiro','o@duodra.co', '********'],
            ['fulano', 'Fulano de Tal','fulano@gmail.com', 'f*******'],
            ['beltrano', 'Beltrano da Silva','beltrano.silva@gmail.com', 'b*******'],
            ['beltrano2', 'Beltrano da Silva','beltrano.silva@gmail.com', 'b*******'],
            ['gringo', 'John Doe','john.doe@gmail.com', 'j*******'],
        ];
        foreach($data as $each){
            $pdo->query("
                INSERT INTO user (login, name, email, password) VALUES ('{$each[0]}','{$each[1]}','{$each[2]}','{$each[3]}');
            ");
        }
        return $pdo;
    }

    public function tearDown()
    {
        $this->object = null;
        $this->mapper = null;
    }

    protected function getMapper()
    {
        $map = new Mapper\Map(
            'Dummy\\Data\\User',
            'user',
            'id',
            [
                'id' => 'id_user',
                'alias' => 'login',
                'name' => 'name',
                'email' => 'email'
            ]
        );
        $mapper = new Mapper($this->object, $map);
        return $mapper;
    }

    public function testFind()
    {
        $results = [
            'all' => $this->object->find($this->mapper),
            'byId' => $this->object->find($this->mapper, ['id'=>2]),
            'byName' => $this->object->find($this->mapper, ['name'=>'Beltrano da Silva']),
            'empty' => $this->object->find($this->mapper, ['email'=>'teste@johndoe.com'])
        ];
        $this->assertCount(5,$results['all']);
        $this->assertCount(0,$results['empty']);
        $this->assertCount(1,$results['byId']);
        $this->assertCount(2,$results['byName']);
        $this->assertInstanceOf('Dummy\\Data\\User',$results['byId'][0]);
        $this->assertInternalType('array',$results['all']);
        $this->assertInternalType('array',$results['byId']);
        $this->assertInternalType('array',$results['empty']);
    }

    public function testSave()
    {
        $this->markTestIncomplete();
    }

    public function testDelete()
    {
        $this->markTestIncomplete();
    }
}