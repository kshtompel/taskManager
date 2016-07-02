<?php
/**
 * Created by PhpStorm.
 * User: synthetic
 * Date: 01.07.16
 * Time: 22:16
 */

namespace AppBundle\ServersHandle\ObjectMapper;


use AppBundle\ServersHandle\Action\ActionInterface;
use AppBundle\ServersHandle\Action\ActionParameter;
use AppBundle\ServersHandle\Service\CallableInterface;

interface ParameterExtractorInterface
{
    /**
     * Extract input parameters for generate documentation
     *
     * @param ActionInterface   $action
     * @param CallableInterface $callable
     *
     * @return array|ActionParameter[]
     */
    public function extract(ActionInterface $action, CallableInterface $callable);
}