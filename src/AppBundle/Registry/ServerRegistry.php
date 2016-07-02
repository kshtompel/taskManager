<?php

namespace AppBundle\Registry;


use Symfony\Component\DependencyInjection\ContainerInterface;

class ServerRegistry implements ServerRegistryInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var array
     */
    private $servers = [];

    /**
     * Construct
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Add server to registry
     *
     * @param string $key
     * @param string $serviceId
     *
     * @return ServerRegistry
     */
    public function addServer($key, $serviceId)
    {
        $this->servers[$key] = $serviceId;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getServer($key)
    {
        if (!isset($this->servers[$key])) {
            throw new \Exception(sprintf(
                'Not found server with key "%s".',
                $key
            ));
        }

        $serviceId = $this->servers[$key];

        if (!$this->container->has($serviceId)) {
            throw new \Exception(sprintf(
                'Not found service "%s" for server "%s".',
                $serviceId,
                $key
            ));
        }

        return $this->container->get($serviceId);
    }
}
