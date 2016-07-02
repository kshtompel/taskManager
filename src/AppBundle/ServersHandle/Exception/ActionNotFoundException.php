<?php

namespace AppBundle\ServersHandle\Exception;


/**
 * Control action not found error
 */
class ActionNotFoundException extends \Exception
{
    /**
     * Create a new exception instance with action name
     *
     * @param string $name
     *
     * @return ActionNotFoundException
     */
    public static function create($name)
    {
        $message = sprintf(
            'Not found action with name "%s".',
            $name
        );

        return new static($message);
    }
}
