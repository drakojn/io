<?php
namespace Drakojn\Io\Mapper;

class Map
{
    protected $localName;
    protected $remoteName;
    protected $identifier;
    protected $properties;

    function __construct($localName, $remoteName, $identifier,  array $properties = [])
    {
        $this->localName  = $localName;
        $this->remoteName = $remoteName;
        $this->identifier = $identifier;
        foreach($properties as $localProperty => $remoteProperty){
            $this->addProperty($localProperty, $remoteProperty);
        }
    }

    /**
     * @param string $localName
     */
    public function setLocalName($localName)
    {
        $this->localName = $localName;
    }

    /**
     * @return string
     */
    public function getLocalName()
    {
        return $this->localName;
    }

    /**
     * @param string $remoteName
     */
    public function setRemoteName($remoteName)
    {
        $this->remoteName = $remoteName;
    }

    /**
     * @return string
     */
    public function getRemoteName()
    {
        return $this->remoteName;
    }

    /**
     * @param string $identifier
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
    }
    
    /**
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    public function addProperty($localProperty, $remoteProperty)
    {
        $this->properties[$localProperty] = $remoteProperty;
        return $this;
    }

    public function removeProperty($localProperty)
    {
        unset($this->properties[$localProperty]);
        return $this;
    }

    /**
     * @return array
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * Extract values from a given object
     * @param object $object
     * @return array
     * @throws \InvalidArgumentException
     */
    public function getData($object)
    {
        if(!$this->validateObject($object)){
            throw new \InvalidArgumentException(
                "Given object isn\'t instance of {$this->localName}"
            );
        }
        $reflection = new \ReflectionObject($object);
        $data = [];
        foreach(array_keys($this->properties) as $localProperty){
            $property = $reflection->getProperty($localProperty);
            $property->setAccessible(true);
            $data[$localProperty] = $property->getValue($object);
        }
        return $data;
    }

    /**
     * Checks if given object is an instance of Map's set
     * @param object $object
     * @return bool
     */
    public function validateObject($object)
    {
        return is_a($object, $this->localName);
    }
}
