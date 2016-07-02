<?php
/**
 * Created by PhpStorm.
 * User: synthetic
 * Date: 01.07.16
 * Time: 23:52
 */

namespace AppBundle\Component\Handler\Parameter;


class MethodParameterResolverAndExtractor implements ParameterResolverInterface, ParameterExtractorInterface
{
    /**
     * {@inheritDoc}
     */
    public function resolve(ActionInterface $action, CallableInterface $callable, array $inputParameters)
    {
        $methodParameters = $callable->getReflection()->getParameters();
        $parameters = [];

        foreach ($methodParameters as $methodParameter) {
            $parameterName = $methodParameter->getName();

            if (isset($inputParameters[$parameterName])) {
                $parameters[] = $inputParameters[$parameterName];
            } else {
                if ($methodParameter->isOptional()) {
                    $parameters[] = $methodParameter->getDefaultValue();
                } else {
                    $parameters[] = null;
                }
            }
        }

        return $parameters;
    }

    /**
     * {@inheritDoc}
     */
    public function extract(ActionInterface $action, CallableInterface $callable)
    {
        $reflection = $callable->getReflection();
        $methodParameters = $reflection->getParameters();
        $parameters = [];

        foreach ($methodParameters as $methodParameter) {
            $defaultValue = null;

            if ($methodParameter->isOptional()) {
                $defaultValue = $methodParameter->getDefaultValue();
            }

            $name = $methodParameter->getName();

            $parameters[] = new Parameter(
                $name,
                'string',
                !$methodParameter->isOptional(),
                null,
                $defaultValue
            );
        }

        return $parameters;
    }
}