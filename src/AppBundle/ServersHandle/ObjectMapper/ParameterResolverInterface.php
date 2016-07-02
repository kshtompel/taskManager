<?php

namespace AppBundle\ServersHandle\ObjectMapper;


use AppBundle\ServersHandle\Action\ActionInterface;
use AppBundle\ServersHandle\Service\CallableInterface;

interface ParameterResolverInterface
{
    /**
     * Resolve arguments
     *
     * @param ActionInterface   $action
     * @param CallableInterface $callable
     * @param array             $inputParameters
     *
     * @return array
     */
    public function resolve(ActionInterface $action, CallableInterface $callable, array $inputParameters);
}