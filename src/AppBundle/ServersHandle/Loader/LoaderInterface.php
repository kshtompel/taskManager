<?php

namespace AppBundle\ServersHandle\Loader;

use AppBundle\ServersHandle\Action\ActionCollectionInterface;


/**
 * All Server handler loaders should be implemented of this interface
 */
interface LoaderInterface
{
    /**
     * Get all actions
     *
     * @return ActionCollectionInterface
     */
    public function loadActions();
}
