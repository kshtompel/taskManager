<?php

namespace AppBundle\ServersHandle\Action;

/**
 * All API services should be implemented of this interface
 */
interface ActionInterface extends \Serializable
{
    /**
     * Get service name
     *
     * @return string
     */
    public function getName();
}