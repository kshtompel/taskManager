<?php
/**
 * Created by PhpStorm.
 * User: synthetic
 * Date: 01.07.16
 * Time: 23:44
 */

namespace AppBundle\Component\Handler;


use AppBundle\Component\Error\Errors;
use AppBundle\ServersHandle\Action\ActionRegistryInterface;
use AppBundle\ServersHandle\ObjectMapper\ParameterResolverInterface;
use AppBundle\ServersHandle\Service\CallableResolverInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class BaseHandler implements HandlerInterface
{
    /**
     * @var ActionRegistryInterface
     */
    private $actionManager;

    /**
     * @var CallableResolverInterface
     */
    private $callableResolver;

    /**
     * @var ParameterResolverInterface
     */
    private $parameterResolver;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var Errors
     */
    private $errors;

    /**
     * Construct
     *
     * @param ActionRegistryInterface     $actionManager
     * @param CallableResolverInterface  $callableResolver
     * @param ParameterResolverInterface $parameterResolver
     * @param EventDispatcherInterface   $eventDispatcher
     * @param Errors                     $errors
     */
    public function __construct(
        ActionRegistryInterface $actionManager,
        CallableResolverInterface $callableResolver,
        ParameterResolverInterface $parameterResolver,
        EventDispatcherInterface $eventDispatcher,
        Errors $errors = null
    ) {
        $this->actionManager = $actionManager;
        $this->callableResolver = $callableResolver;
        $this->eventDispatcher = $eventDispatcher;
        $this->parameterResolver = $parameterResolver;
        $this->errors = $errors ?: new Errors();
    }

    /**
     * {@inheritDoc}
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * {@inheritDoc}
     */
    public function getActions()
    {
        return $this->actionManager->getActions();
    }

    /**
     * {@inheritDoc}
     */
    public function handle($method, array $parameters)
    {
        try {
            // Get action and callable
            $action = $this->actionManager->getAction($method);
            $callable = $this->callableResolver->resolve($action);

            // Resolve parameters
            $parameters = $this->parameterResolver->resolve($action, $callable, $parameters);

            // Dispatch "pre dispatch" event
            $event = new ActionDispatchEvent($action, $callable, $parameters);
            $this->eventDispatcher->dispatch(ApiEvents::ACTION_PRE_DISPATCH, $event);

            // Call to API method
            $response = $callable->apply($parameters);

            if (null === $response) {
                throw new \RuntimeException(sprintf(
                    'The callable "%s" should be return Response or any values. Can not be empty.',
                    Reflection::getCalledMethod($callable->getReflection())
                ));
            }

            if (!$response instanceof ResponseInterface) {
                // Try transform in listeners.
                $event = new ActionViewEvent($action, $callable, $parameters, $response);
                $this->eventDispatcher->dispatch(ApiEvents::ACTION_VIEW, $event);

                $response = $event->getResponse();

                if (!$response) {
                    throw new \RuntimeException(sprintf(
                        'Not found response after dispatch view event in API method "%s". ' .
                        'You must return response or transform response in view event.',
                        Reflection::getCalledMethod($callable->getReflection())
                    ));
                }

                if (!$response instanceof ResponseInterface) {
                    throw new \RuntimeException(sprintf(
                        'The response after dispatch view event must be a ResponseInterface instance, but "%s" given ' .
                        'for method "%s".',
                        is_object($response) ? get_class($response) : gettype($response)
                    ));
                }
            }

            $event = new ActionDispatchEvent($action, $callable, $parameters, $response);
            $this->eventDispatcher->dispatch(ApiEvents::ACTION_POST_DISPATCH, $event);

            return $response;
        } catch (\Exception $e) {
            $event = new ActionExceptionEvent(isset($action) ? $action : null, $e);
            $this->eventDispatcher->dispatch(ApiEvents::ACTION_EXCEPTION, $event);

            if ($event->hasResponse()) {
                return $event->getResponse();
            }

            throw $e;
        }
    }
}