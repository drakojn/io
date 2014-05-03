<?php
namespace Drakojn\Io\Driver;

use Drakojn\Io\Driver\Descriptor\Ini;
use Drakojn\Io\Driver\Descriptor\Json;
use Drakojn\Io\Driver\Descriptor\Php;
use Drakojn\Io\Mapper;
use Dummy\Data\User;

class FileTest extends DriverTestAbstract
{
    protected $resourcePath = '/tmp/drakojn-io-store/';

    public function setUp()
    {
        //$this->buildFiles();
        $this->object = new File($this->resourcePath);
        $this->mapper = $this->getMapper();
    }

    public function tearDown()
    {
        $this->destroyFiles();
        $this->object = null;
        $this->mapper = null;
    }

    public function getDescriptors()
    {
        return [
            'ini'  => [new Ini],
            'php'  => [new Php],
            'json' => [new Json]
        ];
    }

    protected function destroyFiles()
    {
        foreach (glob($this->resourcePath . 'user/*') as $file) {
            unlink($file);
        }
    }

    protected function buildFiles()
    {
        $base = new User;
        $path = 'user/';
        if (!file_exists($this->resourcePath . $path)) {
            mkdir($this->resourcePath . $path, 0777, true);
        }
        foreach ($this->getDataStore() as $key => $each) {
            $id_user = serialize($key);
            $each[3] = '*******';
            $each    = array_map('serialize', $each);
            $string
                     = <<<STORE
id_user='{$id_user}'
login='{$each[0]}'
name='{$each[1]}'
email='{$each[2]}'
password='{$each[3]}'
STORE;
            file_put_contents($this->resourcePath . $path . $key, $string, LOCK_EX);
            unset($obj);
        }
    }

    protected function buildFixture($descriptor)
    {
        $base = new User;
        $path = 'user/';
        if (!file_exists($this->resourcePath . $path)) {
            mkdir($this->resourcePath . $path, 0777, true);
        }
        foreach ($this->getDataStore() as $key => $each) {
            $current = clone $base;
            $current->setId($key);
            $current->setAlias($each[0]);
            $current->setName($each[1]);
            $current->getEmail($each[3]);
            $string = $descriptor->serialize($this->mapper->getMap(), $current);
            file_put_contents($this->resourcePath . $path . $key, $string, LOCK_EX);
        }
    }

    /**
     * @dataProvider getdescriptors
     */
    public function testFind($descriptor)
    {
        $this->buildFixture($descriptor);
        $this->object->setDescriptor($descriptor);
        parent::testFind();
    }

    /**
     * @dataProvider getdescriptors
     */
    public function testSave($descriptor)
    {
        $this->buildFixture($descriptor);
        $this->object->setDescriptor($descriptor);
        parent::testSave();
    }

    /**
     * @dataProvider getdescriptors
     */
    public function testDelete($descriptor)
    {
        $this->buildFixture($descriptor);
        $this->object->setDescriptor($descriptor);
        parent::testDelete();
    }
}