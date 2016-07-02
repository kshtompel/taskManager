<?php

namespace AppBundle\Registry;

use AppBundle\ServersHandle\Interfaces\ServerInterface;

/**
 * All server registry should be implemented of this interface
 */
interface ServerRegistryInterface
{
    /**
     * Get server
     *
     * @param string $key
     *
     * @return ServerInterface
     *
     * @throws \Exception
     */
    public function getServer($key);
}