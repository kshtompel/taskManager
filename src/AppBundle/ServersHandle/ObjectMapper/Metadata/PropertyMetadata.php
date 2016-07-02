<?php


namespace AppBundle\ServersHandle\ObjectMapper\Metadata;

class PropertyMetadata
{
    /**
     * @var string
     */
    protected $propertyName;

    /**
     * @var string
     */
    protected $fieldName;

    /**
     * Class name for mapping as object
     *
     * @var string
     */
    protected $class;

    /**
     * Collection metadata for mapping as collection (Optional)
     *
     * @var CollectionMetadata
     */
    protected $collectionMetadata;

    /**
     * READ-ONLY. Used only for ReflectionStrategy
     *
     * @var \ReflectionProperty
     */
    public $reflection;

    /**
     * Construct
     *
     * @param string             $propertyName
     * @param string             $fieldName
     * @param string             $class
     * @param CollectionMetadata $collectionMetadata
     */
    public function __construct($propertyName, $fieldName, $class, CollectionMetadata $collectionMetadata = null)
    {
        $this->propertyName = $propertyName;
        $this->fieldName = $fieldName;
        $this->class = $class;
        $this->collectionMetadata = $collectionMetadata;
    }

    /**
     * Get property name
     *
     * @return string
     */
    public function getPropertyName()
    {
        return $this->propertyName;
    }

    /**
     * Get field name
     *
     * @return string
     */
    public function getFieldName()
    {
        return $this->fieldName;
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
     * Get collection metadata
     *
     * @return CollectionMetadata
     */
    public function getCollectionMetadata()
    {
        return $this->collectionMetadata;
    }

    /**
     * Is property collection
     *
     * @return bool
     */
    public function isCollection()
    {
        return (bool) $this->collectionMetadata;
    }
}