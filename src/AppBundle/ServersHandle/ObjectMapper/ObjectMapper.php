<?php

namespace AppBundle\ServersHandle\ObjectMapper;


use AppBundle\Cache\ArrayCache;
use AppBundle\Cache\CachedMetadataFactory;
use AppBundle\Component\Reflection\Reflection;
use AppBundle\ServersHandle\Exception\MapException;
use AppBundle\ServersHandle\Exception\ObjectNotSupportedException;
use AppBundle\ServersHandle\Exception\UnexpectedTypeException;
use Doctrine\Common\Annotations\AnnotationReader;


class ObjectMapper implements ObjectMapperInterface
{
    /**
     * @var MetadataFactoryInterface
     */
    private $metadataFactory;

    /**
     * @var StrategyRegistryInterface
     */
    private $strategyManager;

    /**
     * Construct
     *
     * @param MetadataFactoryInterface $metadataFactory
     * @param StrategyRegistryInterface $strategyManager
     */
    public function __construct(MetadataFactoryInterface $metadataFactory, StrategyRegistryInterface $strategyManager)
    {
        $this->metadataFactory = $metadataFactory;
        $this->strategyManager = $strategyManager;
    }

    /**
     * {@inheritDoc}
     */
    public function getMetadataFactory()
    {
        return $this->metadataFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function isSupported($object, $group = ObjectMetadata::DEFAULT_GROUP)
    {
        if (!is_object($object)) {
            throw UnexpectedTypeException::create($object, 'object');
        }

        $metadata = $this->metadataFactory->load($object, $group);

        return (bool) $metadata;
    }

    /**
     * {@inheritDoc}
     */
    public function map($object, array $parameters, $group = ObjectMetadata::DEFAULT_GROUP)
    {
        $this->doMap($object, $parameters, $group, []);
    }

    /**
     * Create a default object mapper.
     * Use AnnotationLoader for loading metadata
     *
     * @return ObjectMapper
     */
    public static function createDefault()
    {
        $loader = new AnnotationLoader(new AnnotationReader());
        $metadataFactory = new MetadataFactory($loader);
        $cache = new ArrayCache();
        $cachedMetadataFactory = new CachedMetadataFactory($metadataFactory, $cache);
        $strategyManager = StrategyRegistry::createDefault();

        return new static($cachedMetadataFactory, $strategyManager);
    }

    /**
     * Map process
     *
     * @param object         $object
     * @param array          $parameters
     * @param string         $group
     * @param array          $paths
     *
     * @return object
     *
     * @throws MapException
     * @throws ObjectNotSupportedException
     */
    protected function doMap($object, array $parameters, $group, array $paths)
    {
        if (!is_object($object)) {
            throw UnexpectedTypeException::create($object, 'object');
        }

        $metadata = $this->metadataFactory->load($object, $group);

        if (!$metadata) {
            throw new ObjectNotSupportedException(sprintf(
                'The object with class "%s" not supported for mapping in group "%s".',
                get_class($object),
                $group
            ));
        }

        $strategy = $this->strategyManager->get($metadata->getStrategy());

        foreach ($metadata->getProperties() as $propertyMetadata) {
            if (!isset($parameters[$propertyMetadata->getFieldName()])) {
                // Not found parameter for property
                continue;
            }

            // Add path name to paths stack
            $paths[] = $propertyMetadata->getFieldName();

            $value = $parameters[$propertyMetadata->getFieldName()];

            if ($propertyMetadata->getCollectionMetadata()) {
                // Map as new collection
                $value = $this->doMapCollection($propertyMetadata, $value, $group, $paths);
            } else if ($propertyMetadata->getClass()) {
                // Map as new model
                $value = $this->doMapNewObject($propertyMetadata, $value, $group, $paths);
            }

            $strategy->map($propertyMetadata, $object, $value);

            // Pop path name from paths stack
            array_pop($paths);
        }

        return $object;
    }

    /**
     * Process for mapping with collection
     *
     * @param PropertyMetadata $propertyMetadata
     * @param mixed            $parameters
     * @param string           $group
     * @param array            $paths
     *
     * @return object
     *
     * @throws MapException
     */
    protected function doMapCollection(PropertyMetadata $propertyMetadata, $parameters, $group, array $paths)
    {
        if (is_object($parameters) && $parameters instanceof \Iterator) {
            $parameters = iterator_to_array($parameters);
        }

        if (!is_array($parameters)) {
            $this->controlInvalidType($paths, $parameters);
        }

        $collectionMetadata = $propertyMetadata->getCollectionMetadata();
        $collectionClass = $collectionMetadata->getClass();
        $collection = $this->createObjectFromClass($collectionClass);

        if (!$collection instanceof \ArrayAccess) {
            throw new MapException(sprintf(
                'Could not map collection with path "%s". The collection should implement ArrayAccess, but "%s" given.',
                $this->generatePath($paths),
                get_class($collection)
            ));
        }

        foreach ($parameters as $key => $value) {
            // Add path to paths stack
            $paths[] = $key;

            if ($propertyMetadata->getClass()) {
                $value = $this->doMapNewObject($propertyMetadata, $value, $group, $paths);
            }

            if ($collectionMetadata->isSaveKeys()) {
                $collection[$key] =  $value;
            } else {
                $collection[] = $value;
            }

            // Remove path from paths stack
            array_pop($paths);
        }

        return $collection;
    }

    /**
     * Process for mapping new object
     *
     * @param PropertyMetadata $propertyMetadata
     * @param mixed            $parameters
     * @param string           $group
     * @param array            $paths
     *
     * @return object
     */
    protected function doMapNewObject(PropertyMetadata $propertyMetadata, $parameters, $group, array $paths)
    {
        if (is_object($parameters) && $parameters instanceof \Iterator) {
            $parameters = iterator_to_array($parameters);
        }

        if (!is_array($parameters)) {
            $this->controlInvalidType($paths, $parameters);
        }

        $class = $propertyMetadata->getClass();
        $childObject = $this->createObjectFromClass($class);

        $value = $this->doMap($childObject, $parameters, $group, $paths);

        return $value;
    }

    /**
     * Control error with invalid type
     *
     * @param array $paths
     * @param mixed $value
     *
     * @throws MapException
     */
    protected function controlInvalidType(array $paths, $value)
    {
        $message = sprintf(
            'The value of parameter "%s" should be a type of array, but "%s" given.',
            $this->generatePath($paths),
            is_object($value) ? get_class($value) : gettype($value)
        );

        throw new MapException($message);
    }

    /**
     * Generate path string
     *
     * @param array $paths
     *
     * @return string
     */
    protected function generatePath(array $paths)
    {
        if (count($paths) == 1) {
            return $paths[0];
        }

        $firstElement = array_shift($paths);

        return $firstElement . '[' . implode('][', $paths) . ']';
    }

    /**
     * Create a new object by class
     *
     * @param string $class
     *
     * @return object
     */
    protected function createObjectFromClass($class)
    {
        $reflection = Reflection::loadClassReflection($class);

        if (!$reflection->isUserDefined()) {
            // PHP System class
            return $reflection->newInstance();
        }

        $constructor = $reflection->getConstructor();

        if (!$constructor) {
            // Constructor not found
            return $reflection->newInstanceWithoutConstructor();
        }

        $constructorParameters = $constructor->getParameters();

        // Is constructor has required parameter
        $constructorHasRequiredParameter = false;

        foreach ($constructorParameters as $constructorParameter) {
            if (!$constructorParameter->isOptional()) {
                $constructorHasRequiredParameter = true;

                break;
            }
        }

        if ($constructorHasRequiredParameter) {
            return $reflection->newInstanceWithoutConstructor();
        } else {
            return $reflection->newInstance();
        }
    }
}