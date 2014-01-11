<?php
date_default_timezone_set('UTC');
require 'vendor/autoload.php';

$map = [
  'Drakojn\\Io\\Mapper\Map' => 'library/Drakojn/Io/Mapper/Map.php',
  'Drakojn\\Io\\Mapper\MapTest' => 'tests/Drakojn/Io/Mapper/MapTest.php',
  'Drakojn\\Io\\MapperTest' => 'tests/Drakojn/Io/MapperTest.php',
  'Dummy\\Data\\User' => 'tests/Dummy/Data/User.php'
];

spl_autoload_register(
    function($className) use ($map){
        if(isset($map[$className])){
            require $map[$className];
            return true;
        }
    }
);