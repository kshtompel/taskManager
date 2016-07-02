<?php
/**
 * Created by PhpStorm.
 * User: synthetic
 * Date: 01.07.16
 * Time: 23:18
 */

namespace AppBundle\Cache;


class CacheFactory
{
//    /**
//     * @var \Redis
//     */
//    private $redis;

    /**
     * @var bool
     */
    private $debug;

    /**
     * Construct
     *
//     * @param \Redis $redis
     * @param bool   $debug
     */
//    public function __construct(\Redis $redis, $debug)
    public function __construct($debug)
    {
//        $this->redis = $redis;
        $this->debug = $debug;
    }

    /**
     * Create a new cache instance
     *
     * @return CacheInterface
     */
    public function create()
    {
//        if ($this->debug) {
            return new ArrayCache();
//        }

//        return new RedisCache($this->redis);
    }
}