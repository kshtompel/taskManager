<?php


namespace AppBundle\ServersHandle\ObjectMapper\Metadata;

use AppBundle\ServersHandle\ObjectMapper\MetadataLoaderInterface;

class ChainLoader implements MetadataLoaderInterface
{
    /**
     * @var array|MetadataLoaderInterface[]
     */
    protected $loaders = [];

    /**
     * Construct
     *
     * @param array|MetadataLoaderInterface[] $loaders
     */
    public function __construct(array $loaders = [])
    {
        foreach ($loaders as $loader) {
            $this->addLoader($loader);
        }
    }

    /**
     * Add loader
     *
     * @param MetadataLoaderInterface $loader
     *
     * @return ChainLoader
     */
    public function addLoader(MetadataLoaderInterface $loader)
    {
        $this->loaders[spl_object_hash($loader)] = $loader;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function load($object, $group)
    {
        foreach ($this->loaders as $loader) {
            $metadata = $loader->load($object, $group);

            if (null !== $metadata) {
                return $metadata;
            }
        }

        return null;
    }
}