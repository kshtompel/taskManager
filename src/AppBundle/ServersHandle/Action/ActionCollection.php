<?php

namespace AppBundle\ServersHandle\Action;

use AppBundle\ServersHandle\Exception\ActionNotFoundException;


/**
 * Base action collection
 */
class ActionCollection implements ActionCollectionInterface
{
    /**
     * @var array|ActionInterface[]
     */
    private $actions = [];

    /**
     * Construct
     *
     * @param array|ActionInterface[] $actions
     */
    public function __construct(array $actions = [])
    {
        foreach ($actions as $action) {
            $this->addAction($action);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function addAction(ActionInterface $action)
    {
        $this->actions[$action->getName()] = $action;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function addActions(ActionCollectionInterface $actions)
    {
        $actions = iterator_to_array($actions);

        $this->actions = array_merge($this->actions, $actions);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getAction($name)
    {
        if (isset($this->actions[$name])) {
            return $this->actions[$name];
        }

        throw ActionNotFoundException::create($name);
    }

    /**
     * Has action
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasAction($name)
    {
        return isset($this->actions[$name]);
    }

    /**
     * {@inheritDoc}
     */
    public function removeAction($name)
    {
        unset ($this->actions[$name]);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function current()
    {
        return current($this->actions);
    }

    /**
     * {@inheritDoc}
     */
    public function next()
    {
        return next($this->actions);
    }

    /**
     * {@inheritDoc}
     */
    public function key()
    {
        return key($this->actions);
    }

    /**
     * {@inheritDoc}
     */
    public function valid()
    {
        return key($this->actions) !== null;
    }

    /**
     * {@inheritDoc}
     */
    public function rewind()
    {
        return reset($this->actions);
    }

    /**
     * {@inheritDoc}
     */
    public function serialize()
    {
        return serialize([
            $this->actions
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function unserialize($serialized)
    {
        list (
            $this->actions
            ) = unserialize($serialized);
    }

    /**
     * {@inheritDoc}
     */
    public function count()
    {
        return count($this->actions);
    }
}
