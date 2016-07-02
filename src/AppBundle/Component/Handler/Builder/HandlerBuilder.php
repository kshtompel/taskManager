<?php
/**
 * Created by PhpStorm.
 * User: synthetic
 * Date: 01.07.16
 * Time: 23:27
 */

namespace AppBundle\Component\Handler\Builder;


use AppBundle\Component\Error\ErrorFactoryInterface;
use AppBundle\Component\Error\Errors;
use AppBundle\Component\Handler\BaseHandler;
use AppBundle\Component\Handler\HandlerInterface;
use AppBundle\Component\Handler\Parameter\MethodParameterResolverAndExtractor;
use AppBundle\ServersHandle\Action\ActionRegistry;
use AppBundle\ServersHandle\Exception\AlreadyBuildedException;
use AppBundle\ServersHandle\Loader\CallableLoader;
use AppBundle\ServersHandle\Loader\CallableResolver;
use AppBundle\ServersHandle\Loader\ChainLoader;
use AppBundle\ServersHandle\Loader\LoaderInterface;
use AppBundle\ServersHandle\ObjectMapper\ParameterExtractorInterface;
use AppBundle\ServersHandle\ObjectMapper\ParameterResolverInterface;
use AppBundle\ServersHandle\Service\CallableResolverInterface;
use AppBundle\ServersHandle\Service\ChainResolver;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class HandlerBuilder implements HandlerBuilderInterface
{
    /**
     * @var HandlerInterface
     */
    private $handler;

//    /**
//     * @var \FiveLab\Component\Api\Handler\Doc\ExtractorInterface
//     */
//    private $docExtractor;

    /**
     * @var ErrorFactoryInterface[]
     */
    protected $errorFactories = [];

    /**
     * @var Errors
     */
    protected $errors;

    /**
     * @var array|CallableResolverInterface[]
     */
    protected $callableResolvers = [];

    /**
     * @var ChainResolver
     */
    protected $callableResolver;

    /**
     * @var array|LoaderInterface[]
     */
    protected $actionLoaders = [];

    /**
     * @var ChainLoader
     */
    protected $actionLoader;

    /**
     * @var ActionRegistry
     */
    protected $actionRegistry;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var ParameterResolverInterface
     */
    protected $parameterResolver;

    /**
     * @var ParameterExtractorInterface
     */
    protected $parameterExtractor;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Add error factory
     *
     * @param ErrorFactoryInterface $errorFactory
     *
     * @return HandlerBuilder
     */
    public function addErrorFactory(ErrorFactoryInterface $errorFactory)
    {
        $this->errorFactories[spl_object_hash($errorFactory)] = $errorFactory;

        return $this;
    }

    /**
     * Add closure handle
     *
     * @return CallableLoader
     *
     * @throws AlreadyBuildedException
     */
    public function addCallableHandle()
    {
        if ($this->handler) {
            throw new AlreadyBuildedException('The handler already builded.');
        }

        $loader = new CallableLoader();

        $resolver = new CallableResolver();

        $this->addCallableResolver($resolver);
        $this->addActionLoader($loader);

        return $loader;
    }

    /**
     * Add callable resolver
     *
     * @param CallableResolverInterface $callableResolver
     *
     * @return HandlerBuilder
     *
     * @throws AlreadyBuildedException
     */
    public function addCallableResolver(CallableResolverInterface $callableResolver)
    {
        if ($this->handler) {
            throw new AlreadyBuildedException('The handler already builded.');
        }

        $this->callableResolvers[spl_object_hash($callableResolver)] = $callableResolver;

        return $this;
    }

    /**
     * Add action loader
     *
     * @param LoaderInterface $loader
     *
     * @return HandlerBuilder
     *
     * @throws AlreadyBuildedException
     */
    public function addActionLoader(LoaderInterface $loader)
    {
        if ($this->handler) {
            throw new AlreadyBuildedException('The handler already builded.');
        }

        $this->actionLoaders[spl_object_hash($loader)] = $loader;

        return $this;
    }

    /**
     * Set event dispatcher
     *
     * @param EventDispatcherInterface $eventDispatcher
     *
     * @return HandlerBuilder
     *
     * @throws AlreadyBuildedException
     */
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher)
    {
        if ($this->handler) {
            throw new AlreadyBuildedException('The handler already builded.');
        }

        $this->eventDispatcher = $eventDispatcher;

        return $this;
    }

    /**
     * Set parameter resolver
     *
     * @param ParameterResolverInterface $resolver
     *
     * @return HandlerBuilder
     *
     * @throws AlreadyBuildedException
     */
    public function setParameterResolver(ParameterResolverInterface $resolver)
    {
        if ($this->handler) {
            throw new AlreadyBuildedException('The handler already builded.');
        }

        $this->parameterResolver = $resolver;

        return $this;
    }

    /**
     * Set parameter extractor
     *
     * @param ParameterExtractorInterface $extractor
     *
     * @return HandlerBuilder
     *
     * @throws AlreadyBuildedException
     */
    public function setParameterExtractor(ParameterExtractorInterface $extractor)
    {
        if ($this->handler) {
            throw new AlreadyBuildedException('The handler already builded.');
        }

        $this->parameterExtractor = $extractor;

        return $this;
    }

    /**
     * Set logger
     *
     * @param LoggerInterface $logger
     *
     * @return HandlerBuilder
     *
     * @throws AlreadyBuildedException
     */
    public function setLogger(LoggerInterface $logger)
    {
        if ($this->handler) {
            throw new AlreadyBuildedException('The handler already builded.');
        }

        $this->logger = $logger;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function buildHandler()
    {
        if ($this->handler) {
            return $this->handler;
        }

        // Create action loader and action manager
        $this->actionLoader = $this->createActionLoader();
        $this->actionRegistry = $this->createActionRegistry();

        // Create callable resolver
        $this->callableResolver = $this->createCallableResolver();

        // Create errors system
        $this->errors = $this->createErrors();

        if (!$this->eventDispatcher) {
            $this->eventDispatcher = new EventDispatcher();
        }

        if (!$this->parameterResolver) {
            $this->parameterResolver = $this->createParameterResolver();
        }

        if (!$this->parameterExtractor && $this->parameterResolver instanceof ParameterExtractorInterface) {
            $this->parameterExtractor = $this->parameterResolver;
        }

        // Create handler
        $handler = new BaseHandler(
            $this->actionRegistry,
            $this->callableResolver,
            $this->parameterResolver,
            $this->eventDispatcher,
            $this->errors
        );

        return $handler;
    }

    /**
     * {@inheritDoc}
     */
    public function buildDocExtractor()
    {
//        if ($this->docExtractor) {
//            return $this->docExtractor;
//        }
//
//        $actionExtractor = new ActionExtractor($this->callableResolver, $this->parameterExtractor);
//        $this->docExtractor = new Extractor($actionExtractor);
//
//        return $this->docExtractor;
    }

    /**
     * Create parameter resolver
     *
     * @return MethodParameterResolverAndExtractor
     */
    protected function createParameterResolver()
    {
        return new MethodParameterResolverAndExtractor();
    }

    /**
     * Create action loader
     *
     * @return ChainLoader
     */
    protected function createActionLoader()
    {
        return new ChainLoader($this->actionLoaders);
    }

    /**
     * Create action manager
     *
     * @return ActionRegistry
     */
    protected function createActionRegistry()
    {
        return new ActionRegistry($this->actionLoader);
    }

    /**
     * Create callable resolver
     *
     * @return ChainResolver
     */
    protected function createCallableResolver()
    {
        return new ChainResolver($this->callableResolvers);
    }

    /**
     * Create error
     *
     * @return Errors
     */
    protected function createErrors()
    {
        return new Errors($this->errorFactories);
    }
}
