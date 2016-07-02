<?php


namespace AppBundle\ServersHandle\ObjectMapper\Metadata;

class CollectionMetadata
{
    /**
     * @var string
     */
    private $class;

    /**
     * @var bool
     */
    private $saveKeys;

    /**
     * Construct
     *
     * @param string $class
     * @param bool   $saveKeys
     */
    public function __construct($class, $saveKeys)
    {
        $this->class = $class;
        $this->saveKeys = $saveKeys;
    }

    /**
     * Get class
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Is save keys
     *
     * @return bool
     */
    public function isSaveKeys()
    {
        return $this->saveKeys;
    }
}