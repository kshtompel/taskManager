<?php
/**
 * Created by PhpStorm.
 * User: synthetic
 * Date: 01.07.16
 * Time: 23:00
 */

namespace AppBundle\Cache;


interface CacheInterface
{
    /**
     * Fetch data from storage
     *
     * @param string $key
     *
     * @return mixed
     */
    public function get($key);

    /**
     * Has data exists in storage by key
     *
     * @param string $key
     *
     * @return bool
     */
    public function has($key);

    /**
     * Set data to cache storage
     *
     * @param string $key
     * @param mixed  $data
     * @param int    $ttl
     *
     * @return bool
     */
    public function set($key, $data, $ttl = null);

    /**
     * Remove data from storage
     *
     * @param string $key
     *
     * @return bool
     */
    public function remove($key);

    /**
     * Cleanup storage
     *
     * @return bool
     */
    public function cleanup();
}