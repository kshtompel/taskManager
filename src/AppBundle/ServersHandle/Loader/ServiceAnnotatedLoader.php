<?php

namespace AppBundle\ServersHandle\Loader;

use AppBundle\Annotation\Action;
use AppBundle\Component\Reflection\Reflection;
use AppBundle\ServersHandle\Action\ActionCollection;
use AppBundle\ServersHandle\Action\ServiceAction;
use Doctrine\Common\Annotations\Reader;


class ServiceAnnotatedLoader implements LoaderInterface
{
    /**
     * @var array
     */
    private $classes = [];

    /**
     * @var Reader
     */
    private $reader;

    /**
     * Construct
     *
     * @param Reader $reader
     */
    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * Add service to loader
     *
     * @param string $serviceId
     * @param string $class
     *
     * @return ServiceAnnotatedLoader
     */
    public function addService($serviceId, $class)
    {
        $this->classes[$serviceId] = $class;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function loadActions()
    {
        $actions = new ActionCollection();

        foreach ($this->classes as $id => $class) {
            $reflection = Reflection::loadClassReflection($class);

            // Get all methods from class
            $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);

            foreach ($methods as $method) {
                $methodAnnotations = $this->reader->getMethodAnnotations($method);

                foreach ($methodAnnotations as $annotation) {
                    if ($annotation instanceof Action) {
                        if ($method->isStatic()) {
                            throw new \RuntimeException('The static method not supported (@todo).');
                        }

                        $action = new ServiceAction(
                            $annotation->name,
                            $id,
                            $method->getName(),
                            $annotation->validationGroups
                        );

                        $actions->addAction($action);
                    }
                }
            }
        }

        return $actions;
    }
}
