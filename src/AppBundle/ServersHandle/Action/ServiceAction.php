<?php

namespace AppBundle\ServersHandle\Action;

class ServiceAction extends AbstractAction
{
    /**
     * @var string
     */
    protected $serviceId;

    /**
     * @var string
     */
    protected $method;

    /**
     * Construct
     *
     * @param string $name
     * @param string $serviceId
     * @param string $method
     */
    public function __construct($name, $serviceId, $method)
    {
        $this->name = $name;
        $this->serviceId = $serviceId;
        $this->method = $method;
    }

    /**
     * Get service id
     *
     * @return string
     */
    public function getServiceId()
    {
        return $this->serviceId;
    }

    /**
     * Get method
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }
}
