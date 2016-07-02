<?php

namespace AppBundle\ServersHandle\Action;


/**
 * Base abstract action
 */
abstract class AbstractAction implements ActionInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritDoc}
     */
    public function serialize()
    {
        $data = [];

        foreach ($this as $key => $value) {
            $data[$key] = $value;
        }

        return serialize($data);
    }

    /**
     * {@inheritDoc}
     */
    public function unserialize($data)
    {
        $data = unserialize($data);

        foreach ($data as $key => $value) {
            $this->{$key} = $value;
        }
    }
}
