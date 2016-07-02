<?php


namespace AppBundle\ServersHandle\ObjectMapper\Metadata;

interface MetadataLoaderInterface
{
    /**
     * Load metadata for object and group
     *
     * @param object $object
     * @param string $group
     *
     * @return ObjectMetadata
     */
    public function load($object, $group);
}
