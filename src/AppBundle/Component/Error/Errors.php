<?php
/**
 * Created by PhpStorm.
 * User: synthetic
 * Date: 01.07.16
 * Time: 23:30
 */

namespace AppBundle\Component\Error;


class Errors
{
    /**
     * @var array|ErrorFactoryInterface[]
     */
    private $factories = [];

    /**
     * Construct
     *
     * @param array|ErrorFactoryInterface[] $factories
     */
    public function __construct(array $factories = [])
    {
        foreach ($factories as $factory) {
            $this->addFactory($factory);
        }
    }

    /**
     * Get errors
     *
     * @return array
     */
    public function getErrors()
    {
        $errors = [];

        foreach ($this->factories as $factory) {
            foreach ($factory->getErrors() as $code => $message) {
                $errors[$code] = $message;
            }
        }

        return $errors;
    }

    /**
     * Get exceptions
     *
     * @return array
     */
    public function getExceptions()
    {
        $exceptions = [];

        foreach ($this->factories as $factory) {
            foreach ($factory->getExceptions() as $exception => $code) {
                $exceptions[$exception] = $code;
            }
        }

        return $exceptions;
    }

    /**
     * Is has exception in storage
     *
     * @param \Exception $exception
     *
     * @return bool
     */
    public function hasException(\Exception $exception)
    {
        $class = get_class($exception);
        $exceptions = $this->getExceptions();

        return isset($exceptions[$class]);
    }

    /**
     * Get exception code
     *
     * @param \Exception $exception
     *
     * @return int
     *
     * @throws \RuntimeException
     */
    public function getExceptionCode(\Exception $exception)
    {
        $class = get_class($exception);
        $exceptions = $this->getExceptions();

        if (!isset($exceptions[$class])) {
            throw new \RuntimeException(sprintf(
                'Not exist exception "%s" in storage.',
                $class
            ));
        }

        return $exceptions[$class];
    }

    /**
     * Add error factory
     *
     * @param ErrorFactoryInterface $factory
     *
     * @return Errors
     */
    public function addFactory(ErrorFactoryInterface $factory)
    {
        $this->factories[get_class($factory)] = $factory;

        return $this;
    }

    /**
     * Get all factories
     *
     * @return array
     */
    public function getFactories()
    {
        return $this->factories;
    }

    /**
     * Get reserved codes
     *
     * @return array
     */
    public function getReservedCodes()
    {
        $reserved = [];

        /** @var ErrorFactoryInterface|string $factory */
        foreach ($this->factories as $factoryClass => $factory) {
            $reserved[$factoryClass] = $factory->getReservedDiapason();
        }

        return $reserved;
    }

    /**
     * Check reserved codes
     *
     * @throws \Exception
     */
    public function checkReservedCodes()
    {
        $reserved = $this->getReservedCodes();

        // First iterate: check all factory
        foreach ($reserved as $factoryClass => $reservedForFactory) {
            // Second iterate: check in each factory
            foreach ($reserved as $checkInFactory => $reservedInCheckFactory) {
                if ($checkInFactory == $factoryClass) {
                    continue;
                }

                if ($reservedInCheckFactory[0] >= $reservedForFactory[0] &&
                    $reservedInCheckFactory[0] <= $reservedForFactory[1]
                ) {
                    throw new \RuntimeException(sprintf(
                        'The reserved codes for factory "%s" [%d - %d] superimposed on "%s" factory [%d - %d].',
                        $checkInFactory,
                        $reservedInCheckFactory[0],
                        $reservedInCheckFactory[1],
                        $factoryClass,
                        $reservedForFactory[0],
                        $reservedForFactory[1]
                    ));
                }

                if ($reservedInCheckFactory[1] >= $reservedForFactory[0] &&
                    $reservedInCheckFactory[1] <= $reservedForFactory[1]
                ) {
                    throw new \RuntimeException(sprintf(
                        'The reserved codes for factory "%s" [%d - %d] superimposed on "%s" factory [%d - %d].',
                        $checkInFactory,
                        $reservedInCheckFactory[0],
                        $reservedInCheckFactory[1],
                        $factoryClass,
                        $reservedForFactory[0],
                        $reservedForFactory[1]
                    ));
                }
            }
        }
    }
}