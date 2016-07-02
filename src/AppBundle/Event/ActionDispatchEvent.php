<?php
/**
 * Created by PhpStorm.
 * User: synthetic
 * Date: 01.07.16
 * Time: 23:45
 */

namespace AppBundle\Event;


use AppBundle\Component\ResponseInterface;
use AppBundle\ServersHandle\Action\ActionInterface;
use AppBundle\ServersHandle\Service\CallableInterface;
use Symfony\Component\EventDispatcher\Event;

class ActionDispatchEvent extends Event
{
    /**
     * @var ActionInterface
     */
    private $action;

    /**
     * @var CallableInterface
     */
    private $callable;

    /**
     * @var array
     */
    private $parameters;

    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * Construct
     *
     * @param ActionInterface   $action
     * @param CallableInterface $callable
     * @param array             $parameters
     * @param ResponseInterface $response
     */
    public function __construct(
        ActionInterface $action,
        CallableInterface $callable,
        array $parameters,
        ResponseInterface $response = null
    ) {
        $this->action = $action;
        $this->callable = $callable;
        $this->parameters = $parameters;
        $this->response = $response;
    }

    /**
     * Get action
     *
     * @return ActionInterface
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Get callable
     *
     * @return CallableInterface $callable
     */
    public function getCallable()
    {
        return $this->callable;
    }

    /**
     * Get parameters
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Get response. Used only on post dispatch event.
     *
     * @return ResponseInterface|null
     */
    public function getResponse()
    {
        return $this->response;
    }
}
