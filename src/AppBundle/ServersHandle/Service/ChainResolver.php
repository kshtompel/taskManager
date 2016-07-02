<?php
/**
 * Created by PhpStorm.
 * User: synthetic
 * Date: 01.07.16
 * Time: 23:31
 */

namespace AppBundle\ServersHandle\Service;


use AppBundle\ServersHandle\Action\ActionInterface;
use AppBundle\ServersHandle\Exception\ActionNotSupportedException;

class ChainResolver implements CallableResolverInterface
{
    /**
     * @var array|CallableResolverInterface[]
     */
    private $resolvers = [];

    /**
     * Construct
     *
     * @param array|CallableResolverInterface[] $resolvers
     */
    public function __construct(array $resolvers = [])
    {
        foreach ($resolvers as $resolver) {
            $this->addResolver($resolver);
        }
    }

    /**
     * Add resolver
     *
     * @param CallableResolverInterface $resolver
     *
     * @return ChainResolver
     */
    public function addResolver(CallableResolverInterface $resolver)
    {
        $this->resolvers[spl_object_hash($resolver)] = $resolver;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function isSupported(ActionInterface $action)
    {
        foreach ($this->resolvers as $resolver) {
            if ($resolver->isSupported($action)) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function resolve(ActionInterface $action)
    {
        foreach ($this->resolvers as $resolver) {
            if ($resolver->isSupported($action)) {
                return $resolver->resolve($action);
            }
        }

        throw new ActionNotSupportedException(sprintf(
            'Can not resolve callback for action "%s".',
            get_class($action)
        ));
    }
}