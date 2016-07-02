<?php
/**
 * Created by PhpStorm.
 * User: synthetic
 * Date: 01.07.16
 * Time: 23:03
 */

namespace AppBundle\ServersHandle\ObjectMapper\Metadata;


interface LoaderInterface
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