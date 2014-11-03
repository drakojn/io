<?php
namespace Drakojn\Io;

use Drakojn\Io\Driver\Pdo;
use Dummy\Data\User;
use Pdo as PHPDataObject;

class MapperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Mapper
     */
    protected $object;
    protected $map;
    protected $driver;

    protected function setUp()
    {
        $driver = $this->getDriver();
        $map = $this->getMap();
        $this->object = new Mapper($driver, $map);
    }

    protected function tearDown()
    {
        $this->object = null;
    }

    protected function getMap()
    {
        if(!$this->map){
            $this->map = new Mapper\Map(
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

        }
        return $this->map;
    }

    protected function getDriver()
    {
        if(!$this->driver){
            //TODO: apply some randomness when more drivers been built
            $this->driver = $this->getPdoDriver();
        }
        return $this->driver;
    }

    protected function getPdoDriver()
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
                INSERT INTO user (login, name, email, password)
                VALUES ('{$each[0]}','{$each[1]}','{$each[2]}','{$each[3]}');
            ");
        }
        $driver = new Pdo($pdo);
        return $driver;
    }

    /**
     * @covers Drakojn\Io\Mapper::getDriver
     */
    public function testGetDriver()
    {
        $this->assertInstanceOf('Drakojn\\Io\\DriverInterface',$this->object->getDriver());
    }

    /**
     * @covers Drakojn\Io\Mapper::getMap
     */
    public function testGetMap()
    {
        $this->assertInstanceOf('Drakojn\\Io\\Mapper\\Map',$this->object->getMap());
    }

    /**
     * @covers Drakojn\Io\Mapper::find
     */
    public function testFind()
    {
        $results = [
            'all' => $this->object->find(),
            'byId' => $this->object->find(['id'=>2]),
            'byName' => $this->object->find(['name'=>'Beltrano da Silva']),
            'empty' => $this->object->find(['email'=>'teste@johndoe.com'])
        ];
        $user = $results['all'][0];
        $this->assertCount(5,$results['all']);
        $this->assertCount(0,$results['empty']);
        $this->assertCount(1,$results['byId']);
        $this->assertCount(2,$results['byName']);
        $this->assertInstanceOf('Dummy\\Data\\User',$results['byId'][0]);
        $this->assertInternalType('array',$results['all']);
        $this->assertInternalType('array',$results['byId']);
        $this->assertInternalType('array',$results['empty']);
        $this->assertObjectNotHasAttribute('password',$user);
    }

    /**
     * @covers Drakojn\Io\Mapper::find
     */
    public function testFindAll()
    {
        $results = $this->object->find();
        $user = $results[0];
        $this->assertCount(5,$results);
        $this->assertInstanceOf('Dummy\\Data\\User',$user);
        $this->assertInternalType('array',$results);
        $this->assertObjectNotHasAttribute('password',$user);
    }

    /**
     * @covers Drakojn\Io\Mapper::findByIdentifier
     */
    public function testFindByIdentifier()
    {
        $result = $this->object->findByIdentifier(rand(1,5));
        $this->assertInstanceOf('Dummy\\Data\\User',$result);
        $this->assertObjectNotHasAttribute('password',$result);
    }

    /**
     * @covers Drakojn\Io\Mapper::save
     */
    public function testSave()
    {
        $newUser = new User;
        $newUser->setName('The Doctor');
        $newUser->setAlias('doctorwho');
        $newUser->setEmail('the-doctor@tardis.gal');
        $hashControl = spl_object_hash($newUser);
        $result = $this->object->save($newUser);
        $this->assertEquals(true, (boolean) $result);
    }

    /**
     * @covers Drakojn\Io\Mapper::delete
     */
    public function testDelete()
    {
        $all = $this->object->find([]);
        $ourPick = $all[rand(0,(count($all) - 1))];
        $ourClone = clone $ourPick;
        $return = $this->object->delete($ourPick);
        $this->assertTrue((boolean) $return);
        $try = $this->object->find(['id' => $ourClone->getId()]);
        $this->assertEmpty($try);
    }
}
