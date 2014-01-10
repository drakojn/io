<?php
namespace Drakojn\Io\Mapper;

abstract class Map
{
    protected $localName;
    protected $remoteName;
    protected $identifier;
    protected $properties = [];

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


}