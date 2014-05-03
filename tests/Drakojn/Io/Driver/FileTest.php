<?php
namespace Drakojn\Io\Driver;

use Drakojn\Io\Mapper;
use Dummy\Data\User;

class FileTest extends DriverTestAbstract
{
    protected $resourcePath = '/tmp/drakojn-io-store/';

    public function setUp()
    {
        $this->buildFiles();
        $this->object = new File($this->resourcePath);
        $this->mapper = $this->getMapper();
    }

    public function tearDown()
    {
        $this->destroyFiles();
        $this->object = null;
        $this->mapper = null;
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
            $id_user  = serialize($key);
            $each[3] = '*******';
            $each = array_map('serialize', $each);
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
}