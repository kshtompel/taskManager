<?php

namespace AppBundle\ServersHandle\Service;


interface CallableInterface
{
    const MODE_FUNCTION         = 1;
    const MODE_METHOD           = 2;
    const MODE_METHOD_STATIC    = 3;

    /**
     * Get reflection
     *
     * @return \ReflectionFunction|\ReflectionMethod
     */
    public function getReflection();

    /**
     * Get object
     *
     * @return object|null
     */
    public function getObject();

    /**
     * Get mode
     *
     * @see MODE_* constants
     *
     * @return int
     */
    public function getMode();

    /**
     * Is method
     *
     * @return bool
     */
    public function isMethod();

    /**
     * Is method static
     *
     * @return bool
     */
    public function isMethodStatic();

    /**
     * Is function
     *
     * @return bool
     */
    public function isFunction();

    /**
     * Apply with arguments
     *
     * @param array $arguments
     *
     * @return mixed
     */
    public function apply(array $arguments = []);
}