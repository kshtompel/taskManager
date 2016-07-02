<?php

namespace AppBundle\ServersHandle\Service;


class BaseCallable implements CallableInterface
{
    /**
     * @var \ReflectionFunction|\ReflectionMethod
     */
    private $reflection;

    /**
     * @var object
     */
    private $object;

    /**
     * Construct
     *
     * @param \ReflectionFunction|\ReflectionMethod|\ReflectionFunctionAbstract $reflection
     * @param object                                                            $object
     */
    public function __construct(\ReflectionFunctionAbstract $reflection, $object = null)
    {
        $this->reflection = $reflection;
        $this->object = $object;
    }

    /**
     * {@inheritDoc}
     */
    public function getReflection()
    {
        return $this->reflection;
    }

    /**
     * {@inheritDoc}
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * {@inheritDoc}
     */
    public function getMode()
    {
        if ($this->reflection instanceof \ReflectionFunction) {
            return self::MODE_FUNCTION;
        } elseif ($this->reflection instanceof \ReflectionMethod) {
            if ($this->reflection->isStatic()) {
                return self::MODE_METHOD_STATIC;
            } else {
                return self::MODE_METHOD;
            }
        } else {
            throw new \RuntimeException(sprintf(
                'The reflection "%s" not supported.',
                get_class($this->reflection)
            ));
        }
    }

    /**
     * {@inheritDoc}
     */
    public function isFunction()
    {
        return $this->getMode() === self::MODE_FUNCTION;
    }

    /**
     * {@inheritDoc}
     */
    public function isMethod()
    {
        return $this->getMode() === self::MODE_METHOD;
    }

    /**
     * {@inheritDoc}
     */
    public function isMethodStatic()
    {
        return $this->getMode() === self::MODE_METHOD_STATIC;
    }

    /**
     * {@inheritDoc}
     */
    public function apply(array $arguments = [])
    {
        if ($this->isFunction()) {
            return $this->reflection->invokeArgs($arguments);
        } elseif ($this->isMethodStatic()) {
            return $this->reflection->invokeArgs(null, $arguments);
        } elseif ($this->isMethod()) {
            if (!$this->object) {
                throw new \RuntimeException(
                    'The object is required for invoke method (ReflectionMethod::invokeArgs)'
                );
            }

            return $this->reflection->invokeArgs($this->object, $arguments);
        } else {
            throw new \RuntimeException(sprintf(
                'The reflection "%s" not supported.',
                get_class($this->getReflection())
            ));
        }
    }
}
