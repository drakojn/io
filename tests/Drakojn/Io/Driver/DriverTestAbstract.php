<?php
namespace Drakojn\Io\Driver;

use Drakojn\Io\Mapper;
use Dummy\Data\User;

abstract class DriverTestAbstract extends \PHPUnit_Framework_TestCase
{
    protected $object;
    protected $mapper;

    public function setUp()
    {
    }

    public function tearDown()
    {
    }

    protected function getDataStore()
    {
        return [
            ['duodraco', 'Anderson Casimiro', 'o@duodra.co', '********'],
            ['fulano', 'Fulano de Tal', 'fulano@gmail.com', 'f*******'],
            ['beltrano', 'Beltrano da Silva', 'beltrano.silva@gmail.com', 'b*******'],
            ['beltrano2', 'Beltrano da Silva', 'beltrano.silva@gmail.com', 'b*******'],
            ['gringo', 'John Doe', 'john.doe@gmail.com', 'j*******']
        ];
    }

    protected function getMapper()
    {
        $map    = new Mapper\Map(
            'Dummy\\Data\\User',
            'user',
            'id',
            [
                'id'    => 'id_user',
                'alias' => 'login',
                'name'  => 'name',
                'email' => 'email'
            ]
        );
        $mapper = new Mapper($this->object, $map);
        return $mapper;
    }

    public function testFind()
    {
        $results = [
            'all'    => $this->object->find($this->mapper),
            'byId'   => $this->object->find($this->mapper, ['id' => 2]),
            'byName' => $this->object->find($this->mapper, ['name' => 'Beltrano da Silva']),
            'empty'  => $this->object->find($this->mapper, ['email' => 'teste@johndoe.com'])
        ];
        $user    = $results['all'][0];
        $this->assertCount(5, $results['all']);
        $this->assertCount(0, $results['empty']);
        $this->assertCount(1, $results['byId']);
        $this->assertCount(2, $results['byName']);
        $this->assertInstanceOf('Dummy\\Data\\User', $results['byId'][0]);
        $this->assertInternalType('array', $results['all']);
        $this->assertInternalType('array', $results['byId']);
        $this->assertInternalType('array', $results['empty']);
        $this->assertObjectNotHasAttribute('password', $user);
    }

    public function testSave()
    {
        $newUser = new User;
        $newUser->setName('The Doctor');
        $newUser->setAlias('doctorwho');
        $newUser->setEmail('the-doctor@tardis.gal');
        $hashControl = spl_object_hash($newUser);
        $result      = $this->object->save($this->mapper, $newUser);
        $this->assertInternalType('boolean', $result);
        $this->assertEquals(true, $result);
        $this->assertInstanceOf('Dummy\\Data\\User', $newUser);
        $this->assertNotNull($newUser->getId());
        $this->assertEquals($hashControl, spl_object_hash($newUser));
        $newUser->setEmail('the-doctor@tardis.earth');
        $result = $this->object->save($this->mapper, $newUser);
        $this->assertInternalType('boolean', $result);
        $this->assertEquals(true, $result);
        $this->assertInstanceOf('Dummy\\Data\\User', $newUser);
        $this->assertEquals('the-doctor@tardis.earth', $newUser->getEmail());
        $this->assertEquals($hashControl, spl_object_hash($newUser));
    }

    public function testDelete()
    {
        $all      = $this->object->find($this->mapper);
        $ourPick  = $all[rand(0, (count($all) - 1))];
        $ourClone = clone $ourPick;
        $return   = $this->object->delete($this->mapper, $ourPick);
        $this->assertInternalType('boolean', $return);
        $this->assertTrue($return);
        $try = $this->object->find($this->mapper, ['id' => $ourClone->getId()]);
        $this->assertCount(0, $try);
    }
}
