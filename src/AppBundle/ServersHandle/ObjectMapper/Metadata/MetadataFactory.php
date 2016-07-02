<?php
/**
 * Created by PhpStorm.
 * User: synthetic
 * Date: 01.07.16
 * Time: 23:02
 */

namespace AppBundle\ServersHandle\ObjectMapper\Metadata;


class MetadataFactory implements MetadataFactoryInterface
{
    /**
     * @var LoaderInterface
     */
    private $loader;

    /**
     * Construct
     *
     * @param LoaderInterface $loader
     */
    public function __construct(MetadataLoaderInterface $loader)
    {
        $this->loader = $loader;
    }

    /**
     * {@inheritDoc}
     */
    public function load($object, $group)
    {
        return $this->loader->load($object, $group);
    }
}