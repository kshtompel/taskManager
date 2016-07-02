<?php

namespace AppBundle\ServersHandle\Service;


use AppBundle\ServersHandle\Action\ActionInterface;

interface CallableResolverInterface
{
    /**
     * Is supported action
     *
     * @param ActionInterface $action
     *
     * @return bool
     */
    public function isSupported(ActionInterface $action);

    /**
     * Get reflection for actions
     *
     * @param ActionInterface $action
     *
     * @return CallableInterface
     */
    public function resolve(ActionInterface $action);
}