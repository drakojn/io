<?php
$map = [
  'Drakojn\\Io\\DriverInterface' => 'library/Drakojn/Io/DriverInterface.php',
  'Drakojn\\Io\\Mapper'          => 'library/Drakojn/Io/Mapper.php',
  'Drakojn\\Io\\Mapper\Map'      => 'library/Drakojn/Io/Mapper/Map.php',
  'Drakojn\\Io\\Driver\Pdo'      => 'library/Drakojn/Io/Driver/Pdo.php',
  'Drakojn\\Io\\Mapper\MapTest'  => 'tests/Drakojn/Io/Mapper/MapTest.php',
  'Drakojn\\Io\\Driver\PdoTest'  => 'tests/Drakojn/Io/Driver/PdoTest.php',
  'Drakojn\\Io\\MapperTest'      => 'tests/Drakojn/Io/MapperTest.php',
  'Dummy\\Data\\User'            => 'tests/Dummy/Data/User.php'
];

spl_autoload_register(
    function($className) use ($map){
        if(isset($map[$className])){
            require_once $map[$className];
            return true;
        }
    }
);