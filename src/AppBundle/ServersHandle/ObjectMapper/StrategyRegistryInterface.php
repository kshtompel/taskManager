<?php

namespace AppBundle\ServersHandle\ObjectMapper;


use AppBundle\ServersHandle\Exception\StrategyNotFoundException;

interface StrategyRegistryInterface
{
    /**
     * Get strategy
     *
     * @param string $key
     *
     * @return StrategyInterface
     *
     * @throws StrategyNotFoundException
     */
    public function get($key);
}