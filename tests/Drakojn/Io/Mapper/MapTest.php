<?php
/**
 * Created by PhpStorm.
 * User: duodraco
 * Date: 1/10/14
 * Time: 1:44 PM
 */

namespace Drakojn\Io\Mapper;


class MapTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var Map
     */
    protected $object;

    public function setUp()
    {
        $this->object = new Map(
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

    public function tearDown()
    {
        $this->object = null;
    }

    public function testSetLocalName()
    {
        $name = 'Dummy\\Data\\Role';
        $this->object->setLocalName($name);
        $this->assertEquals($name, $this->object->getLocalName());
    }

    public function testGetLocalName()
    {
        $name = 'Dummy\\Data\\User';
        $this->assertEquals($name, $this->object->getLocalName());
        $this->assertInternalType('string', $this->object->getLocalName());
    }

    public function testSetRemoteName()
    {
        $name = 'role';
        $this->object->setRemoteName($name);
        $this->assertEquals($name, $this->object->getRemoteName());
    }

    public function testGetRemoteName()
    {
        $name = 'user';
        $this->assertEquals($name, $this->object->getRemoteName());
        $this->assertInternalType('string', $this->object->getRemoteName());
    }

    public function testSetIdentifier()
    {
        $identifier = 'idUser';
        $this->object->setIdentifier($identifier);
        $this->assertEquals($identifier, $this->object->getIdentifier());
    }

    public function testGetIdentifier()
    {
        $identifier = 'id';
        $this->assertEquals($identifier, $this->object->getIdentifier());
        $this->assertInternalType('string', $this->object->getIdentifier());
    }

    public function testAddProperty()
    {
        $localProperty = 'password';
        $remoteProperty = 'hash';
        $this->object->addProperty($localProperty, $remoteProperty);
        $this->assertArrayHasKey($localProperty,$this->object->getProperties());
        $this->assertEquals($remoteProperty,$this->object->getProperties()[$localProperty]);
    }

    public function testRemoveProperty()
    {
        $localProperty = 'email';
        $this->object->removeProperty($localProperty);
        $this->assertArrayNotHasKey($localProperty,$this->object->getProperties());
    }

    public function testGetProperties()
    {
        $this->assertInternalType('array',$this->object->getProperties());
        $this->assertArrayHasKey('id',$this->object->getProperties());
        $this->assertArrayNotHasKey('password',$this->object->getProperties());
    }
}
 