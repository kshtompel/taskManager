<?php
/**
 * Created by PhpStorm.
 * User: synthetic
 * Date: 01.07.16
 * Time: 23:35
 */

namespace AppBundle\ServersHandle\Action;


use AppBundle\ServersHandle\Exception\ActionNotFoundException;

interface ActionRegistryInterface
{
    /**
     * Get action by name
     *
     * @param string $name
     *
     * @return ActionInterface
     *
     * @throws ActionNotFoundException
     */
    public function getAction($name);

    /**
     * Get all actions
     *
     * @return ActionCollectionInterface
     */
    public function getActions();

    /**
     * Has action
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasAction($name);
}
