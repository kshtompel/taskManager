<?php
/**
 * Created by PhpStorm.
 * User: synthetic
 * Date: 01.07.16
 * Time: 22:24
 */

namespace AppBundle\ServersHandle\ObjectMapper\Metadata;


interface MetadataFactoryInterface
{
    /**
     * Load metadata for object
     *
     * @param object $object
     * @param string $group
     *
     * @return ObjectMetadata|null
     */
    public function load($object, $group);
}