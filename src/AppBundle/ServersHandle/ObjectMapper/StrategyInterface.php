<?php

namespace AppBundle\ServersHandle\ObjectMapper;


interface StrategyInterface
{
    /**
     * Map parameters to object
     *
     * @param PropertyMetadata $property
     * @param object           $object
     * @param mixed            $value
     */
    public function map(PropertyMetadata $property, $object, $value);
}