<?php
/**
 * Created by PhpStorm.
 * User: synthetic
 * Date: 01.07.16
 * Time: 23:37
 */

namespace AppBundle\ServersHandle\Action;


class CallableAction extends AbstractAction
{
    /**
     * @var callable
     */
    private $callable;

    /**
     * Construct
     *
     * @param string   $name
     * @param callable $callable
     */
    public function __construct(
        $name,
        $callable
    ) {
        if (!is_callable($callable)) {
            throw new \RuntimeException(sprintf(
                'The callback must be a callable, but "%s" given.',
                gettype($callable)
            ));
        }

        $this->name = $name;
        $this->callable = $callable;
    }

    /**
     * Get callable
     *
     * @return callable
     */
    public function getCallable()
    {
        return $this->callable;
    }

    /**
     * {@inheritDoc}
     */
    public function serialize()
    {
        if ($this->callable instanceof \Closure) {
            throw new \RuntimeException('Could not serialize \Closure instance.');
        }

        if (is_array($this->callable) && is_object($this->callable[0])) {
            throw new \RuntimeException(sprintf(
                'Could not serialize action, because you use method of object "%s".',
                get_class($this->callable[0])
            ));
        }

        return parent::serialize();
    }
}
