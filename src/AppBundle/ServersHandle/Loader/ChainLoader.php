<?php
/**
 * Created by PhpStorm.
 * User: synthetic
 * Date: 01.07.16
 * Time: 23:50
 */

namespace AppBundle\ServersHandle\Loader;


use AppBundle\ServersHandle\Action\ActionCollection;
use AppBundle\ServersHandle\Action\ActionCollectionInterface;

class ChainLoader implements LoaderInterface
{
    /**
     * @var array|LoaderInterface[]
     */
    private $loaders = [];

    /**
     * Construct
     *
     * @param array|LoaderInterface[] $loaders
     */
    public function __construct(array $loaders = [])
    {
        foreach ($loaders as $loader) {
            $this->addLoader($loader);
        }
    }

    /**
     * Add loader
     *
     * @param LoaderInterface $loader
     *
     * @return ChainLoader
     */
    public function addLoader(LoaderInterface $loader)
    {
        $this->loaders[spl_object_hash($loader)] = $loader;

        return $this;
    }

    /**
     * Get all actions
     *
     * @return ActionCollectionInterface
     */
    public function loadActions()
    {
        $actions = new ActionCollection();

        foreach ($this->loaders as $loader) {
            $childActions = $loader->loadActions();

            $actions->addActions($childActions);
        }

        return $actions;
    }
}