<?php

namespace AppBundle\ServersHandle\ObjectMapper;


use AppBundle\Component\Reflection\Reflection;
use AppBundle\Component\RequestInterface;
use AppBundle\ServersHandle\Action\ActionInterface;
use AppBundle\ServersHandle\Action\ActionParameter;
use AppBundle\ServersHandle\Service\CallableInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ObjectMapperParameterResolverAndExtractor implements ParameterResolverInterface, ParameterExtractorInterface
{
    /**
     * @var ObjectMapperInterface
     */
    private $objectMapper;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var bool
     */
    private $debug;

    /**
     * Construct
     *
     * @param ObjectMapperInterface              $objectMapper
     * @param ValidatorInterface                 $validator
     * @param LoggerInterface                    $logger
     * @param bool                               $debug
     */
    public function __construct(
        ObjectMapperInterface $objectMapper,
        ValidatorInterface $validator = null,
        LoggerInterface $logger = null,
        $debug = false
    ) {
        $this->objectMapper = $objectMapper;
        $this->validator = $validator;
        $this->logger = $logger;
        $this->debug = $debug;
    }

    /**
     * {@inheritDoc}
     */
    public function resolve(ActionInterface $action, CallableInterface $callable, array $inputArguments)
    {
        $arguments = array();

        $requestParameter = null;
        $parameters = $callable->getReflection()->getParameters();

        foreach ($parameters as $parameter) {
            if (isset($arguments[$parameter->getName()])) {
                throw new \RuntimeException(sprintf(
                    'Many parameters with one name "%s" in method "%s".',
                    $parameter->getName(),
                    Reflection::getCalledMethod($callable->getReflection())
                ));
            }

            if ($class = $parameter->getClass()) {
                if ($class->implementsInterface('FiveLab\Component\Api\Request\RequestInterface')) {
                    if ($requestParameter) {
                        throw new \LogicException(sprintf(
                            'The request already declared in parameter with name "%s" for method "%s".',
                            $requestParameter,
                            Reflection::getCalledMethod($callable->getReflection())
                        ));
                    }
                    $requestParameter = $parameter->getName();

                    $request = $this->resolveRequest($action, $callable, $inputArguments, $parameter);

                    $arguments[$parameter->getName()] = $request;

                    continue;
                }
            }

            if ($parameter->isDefaultValueAvailable()) {
                $arguments[$parameter->getName()] = $parameter->getDefaultValue();
            } else {
                $arguments[$parameter->getName()] = null;
            }
        }

        if (!$requestParameter && $this->debug) {
            if ($this->logger) {
                $this->logger->warning(sprintf(
                    'Not found request parameter in arguments for callable "%s". ' .
                    'Request parameter should implement "FiveLab\Component\Api\Request\RequestInterface"',
                    Reflection::getCalledMethod($callable->getReflection())
                ));
            }
        }

        return $arguments;
    }

    /**
     * {@inheritDoc}
     */
    public function extract(ActionInterface $action, CallableInterface $callable)
    {
        /** @var \ReflectionClass $requestClass */
        $requestClass = null;

        $inputParameters = $callable->getReflection()->getParameters();

        foreach ($inputParameters as $parameter) {
            if ($class = $parameter->getClass()) {
                if ($class->implementsInterface('FiveLab\Component\Api\Request\RequestInterface')) {
                    $requestClass = $class;
                    break;
                }
            }
        }

        if (!$requestClass) {
            return [];
        }

        $requestParameters = [];
        $requestObject = $requestClass->newInstance();

        $objectMetadata = $this->objectMapper->getMetadataFactory()
            ->load($requestObject, ObjectMetadata::DEFAULT_GROUP);

        if (!$objectMetadata) {
            // Can not load metadata for object
            return [];
        }

        foreach ($objectMetadata->getProperties() as $property) {
            if (!$property->reflection) {
                $property->reflection = new \ReflectionProperty(
                    $requestClass->getName(),
                    $property->getPropertyName()
                );
            }

            $docBlock = new DocBlock($property->reflection);

            $content = $docBlock->getShortDescription();
            $typeTags = $docBlock->getTagsByName('var');
            $type = null;

            if ($typeTags) {
                /** @var \phpDocumentor\Reflection\DocBlock\Tag\VarTag $typeTag */
                $typeTag = array_pop($typeTags);
                $type = $typeTag->getType();

                //$type = $this->formatPHPType($type);
            }

            $defaultValue = null;
            if ($property->reflection->isDefault()) {
                if (!$property->reflection->isPublic()) {
                    $property->reflection->setAccessible(true);
                }

                $defaultValue = $property->reflection->getValue($requestObject);
            }

            $parameter = new ActionParameter(
                $property->getFieldName(),
                $type,
                $this->isPropertyRequired($property->reflection),
                $content,
                $defaultValue
            );

            $requestParameters[$property->getFieldName()] = $parameter;
        }

        return $requestParameters;
    }

    /**
     * Resolve request parameter
     *
     * @param ActionInterface      $action
     * @param CallableInterface    $callable
     * @param array                $inputArguments
     * @param \ReflectionParameter $parameter
     *
     * @return object
     *
     * @throws \Exception
     */
    private function resolveRequest(
        ActionInterface $action,
        CallableInterface $callable,
        array $inputArguments,
        \ReflectionParameter $parameter
    ) {
        $class = $parameter->getClass();

        if ($class->isInterface()) {
            throw new \RuntimeException(sprintf(
                'Could not create instance via interface for parameter "%s" in method "%s". ' .
                'You must set the class for type hinting.',
                $parameter->getName(),
                Reflection::getCalledMethod($callable->getReflection())
            ));
        }

        if ($class->isAbstract()) {
            throw new \RuntimeException(sprintf(
                'Could not create instance via abstract class for parameter "%s" in method "%s". ' .
                'You must set the real class for type hinting.',
                $parameter->getName(),
                Reflection::getCalledMethod($callable->getReflection())
            ));
        }

        /** @var RequestInterface $request */
        $request = $class->newInstance();

        // Map arguments
        $this->objectMapper->map($request, $inputArguments, ObjectMetadata::DEFAULT_GROUP);

        // Validate process
        if ($this->validator) {
            if ($this->validator instanceof VarTagValidatorInterface) {
                $violationList = $this->validator->validateObjectByVarTags($request);

                if (count($violationList)) {
                    throw ViolationListException::create($violationList);
                }
            }

            $violationList = $this->validator->validate($request);

            if (count($violationList)) {
                throw ViolationListException::create($violationList);
            }
        }

        return $request;
    }

    /**
     * Is required property
     *
     * @param \ReflectionProperty $property
     *
     * @return bool
     */
    private function isPropertyRequired(\ReflectionProperty $property)
    {
        if (!$this->validator) {
            // Can not check...
            return true;
        }

        $metadata = $this->validator->getMetadataFor($property->getDeclaringClass()->getName());

        if (!$metadata instanceof ClassMetadata) {
            return true;
        }

        $propertyMetadata = $metadata->getPropertyMetadata($property->getName());

        if ($propertyMetadata) {
            // @todo: merge all metadata?
            $propertyMetadata = array_pop($propertyMetadata);

            $constraints = $propertyMetadata->findConstraints('Default');

            foreach ($constraints as $constraint) {
                if ($constraint instanceof NotBlank) {
                    return true;
                }
            }
        }

        return false;
    }
}