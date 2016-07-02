<?php

namespace AppBundle\ServersHandle\ObjectMapper;


interface ObjectMapperInterface
{
    /**
     * Get metadata factory
     *
     * @return MetadataFactoryInterface
     */
    public function getMetadataFactory();

    /**
     * Is object supported
     *
     * @param object $object
     * @param string $group
     *
     * @return bool
     */
    public function isSupported($object, $group = ObjectMetadata::DEFAULT_GROUP);

    /**
     * Map parameters for object
     *
     * @param object $object
     * @param array  $parameters
     * @param string $group
     */
    public function map($object, array $parameters, $group = ObjectMetadata::DEFAULT_GROUP);
}