<?php
namespace Drakojn\Io;

interface DriverInterface
{
    public function find(Mapper $mapper, array $query = []);

    public function save(Mapper $mapper, $object);

    public function delete(Mapper $mapper, $object);
}