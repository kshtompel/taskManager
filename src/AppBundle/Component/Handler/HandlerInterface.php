<?php
/**
 * Created by PhpStorm.
 * User: synthetic
 * Date: 01.07.16
 * Time: 23:27
 */

namespace AppBundle\Component\Handler;


interface HandlerInterface
{
    /**
     * Get errors
     *
     * @return \FiveLab\Component\Error\Errors
     */
    public function getErrors();

    /**
     * Get actions
     *
     * @return \FiveLab\Component\Api\SMD\Action\ActionCollection|\FiveLab\Component\Api\SMD\Action\ActionInterface[]
     */
    public function getActions();

    /**
     * Handle
     *
     * @param string $method     The method name for call
     * @param array  $parameters Named parameters
     *
     * @return \FiveLab\Component\Api\Response\ResponseInterface
     */
    public function handle($method, array $parameters);
}
