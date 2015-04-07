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

    protected function destroyFiles($dir = null)
    {
        if (!$dir) {
            $dir = $this->resourcePath . 'user';
        }
        $files = array_diff(scandir($dir), array('.', '..'));
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? $this->destroyFiles("$dir/$file") : unlink("$dir/$file");
        }
        clearstatcache();
        return rmdir($dir);
    }

    protected function buildFixture($descriptor)
    {
        $base = new User;
        $path = 'user/';
        if (!file_exists($this->resourcePath . $path)) {
            mkdir($this->resourcePath . $path, 0777, true);
        }
        foreach ($this->getDataStore() as $key => $each) {
            $id = $key + 1;
            $current = clone $base;
            $current->setId($id);
            $current->setAlias($each[0]);
            $current->setName($each[1]);
            $current->setEmail($each[2]);
            $string = $descriptor->serialize($this->mapper->getMap(), $current);
            file_put_contents($this->resourcePath . $path . $id, $string, LOCK_EX);
        }
        clearstatcache();
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
