<?php
/**
 * Created by PhpStorm.
 * User: synthetic
 * Date: 01.07.16
 * Time: 23:36
 */

namespace AppBundle\ServersHandle\Loader;


use AppBundle\ServersHandle\Action\ActionCollection;

class CallableLoader implements LoaderInterface
{
    /**
     * @var array|\Closure[]
     */
    private $actions = [];

    /**
     * Add closure action
     *
     * @param string   $name
     * @param callable $callable
     *
     * @return CallableLoader
     */
    public function addCallable($name, $callable)
    {
        $this->actions[$name] = new CallableAction($name, $callable);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function loadActions()
    {
        return new ActionCollection($this->actions);
    }
}