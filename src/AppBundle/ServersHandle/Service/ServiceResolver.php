<?php

namespace AppBundle\ServersHandle\Service;

use AppBundle\Component\Reflection\Reflection;
use AppBundle\ServersHandle\Action\ActionInterface;
use AppBundle\ServersHandle\Action\ServiceAction;
use AppBundle\ServersHandle\Exception\UnexpectedTypeException;
use Symfony\Component\DependencyInjection\ContainerInterface;


class ServiceResolver implements CallableResolverInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

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
     * {@inheritDoc}
     */
    public function isSupported(ActionInterface $action)
    {
        return $action instanceof ServiceAction;
    }

    /**
     * {@inheritDoc}
     */
    public function resolve(ActionInterface $action)
    {
        if (!$action instanceof ServiceAction) {
            throw UnexpectedTypeException::create($action, ServiceAction::class);
        }

        $serviceId = $action->getServiceId();
        $method = $action->getMethod();

        if (!$this->container->has($serviceId)) {
            throw new \RuntimeException(sprintf(
                'Can not resolve action, because the service "%s" not found.',
                $serviceId
            ));
        }

        $service = $this->container->get($serviceId);
        $reflection = Reflection::loadClassReflection($service);
        $method = $reflection->getMethod($method);

        return new BaseCallable($method, $service);
    }
}
