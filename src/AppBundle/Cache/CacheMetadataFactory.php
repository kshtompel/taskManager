<?php
/**
 * Created by PhpStorm.
 * User: synthetic
 * Date: 01.07.16
 * Time: 22:59
 */

namespace AppBundle\Cache;

use AppBundle\ServersHandle\ObjectMapper\MetadataFactoryInterface;


/**
 * Cached metadata factory for caching metadata
 */
class CachedMetadataFactory implements MetadataFactoryInterface
{
    /**
     * @var MetadataFactoryInterface
     */
    private $delegate;

    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * Construct
     *
     * @param MetadataFactoryInterface $delegate
     * @param CacheInterface           $cache
     */
    public function __construct(MetadataFactoryInterface $delegate, CacheInterface $cache)
    {
        $this->delegate = $delegate;
        $this->cache = $cache;
    }

    /**
     * {@inheritDoc}
     */
    public function load($object, $group)
    {
        $class = get_class($object);
        $key = 'object_mapper:' . $class . ':' . $group;

        $metadata = $this->cache->get($key);

        if (!$metadata) {
            $metadata = $this->delegate->load($object, $group);
            $this->cache->set($key, $metadata);
        }

        return $metadata;
    }
}