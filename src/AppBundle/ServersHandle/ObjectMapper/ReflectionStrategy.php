<?php
/**
 * Created by PhpStorm.
 * User: synthetic
 * Date: 01.07.16
 * Time: 23:22
 */

namespace AppBundle\ServersHandle\ObjectMapper;


use AppBundle\Component\Reflection\Reflection;
use AppBundle\ServersHandle\Exception\UnexpectedTypeException;

class ReflectionStrategy implements StrategyInterface
{
    /**
     * {@inheritDoc}
     */
    public function map(PropertyMetadata $property, $object, $value)
    {
        if (!is_object($object)) {
            throw UnexpectedTypeException::create($object, 'object');
        }

        if (!$property->reflection) {
            $objectReflection = Reflection::loadObjectReflection($object);
            $propertyName = $property->getPropertyName();
            $propertyReflection = $objectReflection->getProperty($propertyName);

            if (!$propertyReflection->isPublic()) {
                $propertyReflection->setAccessible(true);
            }

            $property->reflection = $propertyReflection;
        }

        $property->reflection->setValue($object, $value);
    }
}