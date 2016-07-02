<?php
/**
 * Created by PhpStorm.
 * User: synthetic
 * Date: 01.07.16
 * Time: 23:35
 */

namespace AppBundle\ServersHandle\Action;


use AppBundle\ServersHandle\Loader\LoaderInterface;

class ActionRegistry implements ActionRegistryInterface
{
    /**
     * @var LoaderInterface
     */
    private $loader;

    /**
     * @var ActionCollection
     */
    private $actionCollection;

    /**
     * Construct
     *
     * @param LoaderInterface $loader
     */
    public function __construct(LoaderInterface $loader)
    {
        $this->loader = $loader;
    }

    /**
     * {@inheritDoc}
     */
    public function getActions()
    {
        if (null !== $this->actionCollection) {
            return $this->actionCollection;
        }

        $this->actionCollection = $this->loader->loadActions();

        return $this->actionCollection;
    }

    /**
     * {@inheritDoc}
     */
    public function getAction($name)
    {
        return $this->getActions()->getAction($name);
    }

    /**
     * {@inheritDoc}
     */
    public function hasAction($name)
    {
        return $this->getActions()->hasAction($name);
    }
}
