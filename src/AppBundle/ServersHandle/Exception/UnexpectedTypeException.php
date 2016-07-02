<?php

namespace AppBundle\ServersHandle\Exception;


class UnexpectedTypeException extends \InvalidArgumentException
{
    /**
     * Create a new exception
     *
     * @param string|object $actual
     * @param string|object $expected
     * @param int           $code
     * @param \Exception    $prev
     *
     * @return UnexpectedTypeException
     */
    public static function create($actual, $expected, $code = 0, \Exception $prev = null)
    {
        $message = sprintf(
            'The value must be a type of %s, %s given.',
            is_object($expected) ? get_class($expected) : (is_scalar($expected) ? $expected : gettype($expected)),
            is_object($actual) ? get_class($actual) : (is_scalar($actual) ? $actual : gettype($actual))
        );

        return new static($message, $code, $prev);
    }
}