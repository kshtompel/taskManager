<?php

namespace AppBundle\ServersHandle\Action;

/**
 * All action collections should be implemented of this interface
 */
interface ActionCollectionInterface extends \Iterator, \Countable, \Serializable
{
    /**
     * Add action to collection
     *
     * @param ActionInterface $action
     */
    public function addAction(ActionInterface $action);

    /**
     * Add actions
     *
     * @param ActionCollectionInterface $actions
     */
    public function addActions(ActionCollectionInterface $actions);

    /**
     * Get action from collection
     *
     * @param string $name
     *
     * @return ActionInterface
     *
     * @throws \FiveLab\Component\Api\SMD\Exception\ActionNotFoundException
     */
    public function getAction($name);

    /**
     * Has action
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasAction($name);

    /**
     * Remove name
     *
     * @param string $name
     */
    public function removeAction($name);
}
