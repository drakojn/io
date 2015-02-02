<?php
$map = [
    'Drakojn\\Io\\DriverInterface'                       => 'library/Drakojn/Io/DriverInterface.php',
    'Drakojn\\Io\\Mapper'                                => 'library/Drakojn/Io/Mapper.php',
    'Drakojn\\Io\\Mapper\Map'                            => 'library/Drakojn/Io/Mapper/Map.php',
    'Drakojn\\Io\\Driver\Pdo'                            => 'library/Drakojn/Io/Driver/Pdo.php',
    'Drakojn\\Io\\Driver\\SQL\\Action'                   => 'library/Drakojn/Io/Driver/SQL/Action.php',
    'Drakojn\\Io\\Driver\\SQL\\Select'                   => 'library/Drakojn/Io/Driver/SQL/Select.php',
    'Drakojn\\Io\\Driver\\SQL\\Delete'                   => 'library/Drakojn/Io/Driver/SQL/Delete.php',
    'Drakojn\\Io\\Driver\\SQL\\Insert'                   => 'library/Drakojn/Io/Driver/SQL/Insert.php',
    'Drakojn\\Io\\Driver\\SQL\\Update'                   => 'library/Drakojn/Io/Driver/SQL/Update.php',
    'Drakojn\\Io\\Driver\AbstractDriver'                 => 'library/Drakojn/Io/Driver/AbstractDriver.php',
    'Drakojn\\Io\\Driver\Stream'                         => 'library/Drakojn/Io/Driver/Stream.php',
    'Drakojn\\Io\\Driver\File'                           => 'library/Drakojn/Io/Driver/File.php',
    'Drakojn\\Io\\Driver\Descriptor\DescriptorInterface' => 'library/Drakojn/Io/Driver/Descriptor/DescriptorInterface.php',
    'Drakojn\\Io\\Driver\Descriptor\AbstractDescriptor'  => 'library/Drakojn/Io/Driver/Descriptor/AbstractDescriptor.php',
    'Drakojn\\Io\\Driver\Descriptor\Ini'                 => 'library/Drakojn/Io/Driver/Descriptor/Ini.php',
    'Drakojn\\Io\\Driver\Descriptor\Php'                 => 'library/Drakojn/Io/Driver/Descriptor/Php.php',
    'Drakojn\\Io\\Driver\Descriptor\Json'                => 'library/Drakojn/Io/Driver/Descriptor/Json.php',
    'Drakojn\\Io\\Mapper\MapTest'                        => 'tests/Drakojn/Io/Mapper/MapTest.php',
    'Drakojn\\Io\\Driver\DriverTestAbstract'             => 'tests/Drakojn/Io/Driver/DriverTestAbstract.php',
    'Drakojn\\Io\\Driver\PdoTest'                        => 'tests/Drakojn/Io/Driver/PdoTest.php',
    'Drakojn\\Io\\Driver\FileTest'                       => 'tests/Drakojn/Io/Driver/FileTest.php',
    'Drakojn\\Io\\MapperTest'                            => 'tests/Drakojn/Io/MapperTest.php',
    'Dummy\\Data\\User'                                  => 'tests/Dummy/Data/User.php'
];
spl_autoload_register(
    function ($className) use ($map) {
        if (isset($map[$className])) {
            require_once $map[$className];
            return true;
        }
    }
);
