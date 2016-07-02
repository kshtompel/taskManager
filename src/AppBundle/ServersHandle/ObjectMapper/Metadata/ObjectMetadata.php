<?php


namespace AppBundle\ServersHandle\ObjectMapper\Metadata;


class ObjectMetadata
{
    const STRATEGY_REFLECTION   = 'reflection';

    const DEFAULT_GROUP         = 'Default';

    /**
     * @var string
     */
    protected $strategy;

    /**
     * @var array|PropertyMetadata[]
     */
    protected $properties = [];

    /**
     * READ-ONLY. Used only for ReflectionStrategy
     *
     * @var \ReflectionObject
     */
    public $reflection;

    /**
     * Construct
     *
     * @param string $strategy
     * @param array  $properties
     */
    public function __construct($strategy, array $properties)
    {
        $this->strategy = $strategy;
        $this->properties = $properties;
    }

    /**
     * Get strategy
     *
     * @return string
     */
    public function getStrategy()
    {
        return $this->strategy;
    }

    /**
     * Get properties
     *
     * @return array|PropertyMetadata[]
     */
    public function getProperties()
    {
        return $this->properties;
    }
}