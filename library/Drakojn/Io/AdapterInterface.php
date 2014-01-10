<?php
namespace Drakojn\Io;

interface AdapterInterface
{
    public function find(Mapper $mapper, array $query = []);

    public function save(Mapper $mapper, $object);

    public function delete(Mapper $mapper, $object);
}