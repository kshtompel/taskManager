<?php

namespace AppBundle\DependencyInjection;

use AppBundle\DependencyInjection\Configuration\Configuration;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

class AppExtension extends Extension implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $this->buildActions($container, $config['servers']);
    }

    public function process(ContainerBuilder $container)
    {
        foreach ($container->findTaggedServiceIds('app.action') as $serviceId => $tags)
        {
            foreach ($tags as $tagInfo) {
                try {
                    if (empty($tagInfo['server'])) {
                        throw new \RuntimeException('The tag option "server" is required for API action');
                    }

                    $actionsDefinition = $container->getDefinition($serviceId);

                    if ($actionsDefinition instanceof DefinitionDecorator) {
                        $class = $container->getDefinition($actionsDefinition->getParent())->getClass();
                    } else {
                        $class = $actionsDefinition->getClass();
                    }

                    $container
                        ->getDefinition(sprintf(
                            'app.%s.action.loader.annotated',
                            $tagInfo['server']
                        ))
                        ->addMethodCall('addService', [
                            $serviceId,
                            $class
                        ]);
                } catch (\Exception $e) {
                    throw new \RuntimeException(sprintf(
                        'Can not compile api action with service id "%s".',
                        $serviceId
                    ), 0, $e);
                }
            }
        }
    }

    /**
     * Build servers
     *
     * @param ContainerBuilder $container
     * @param array            $serversInfo
     */
    private function buildActions(ContainerBuilder $container, array $serversInfo)
    {
        foreach ($serversInfo as $server => $info) {
            $annotatedLoader = $container
                ->setDefinition(
                    sprintf('app.%s.action.loader.annotated', $server),
                    new DefinitionDecorator('app.action.loader.annotated.abstract')
                );

            $container
                ->setDefinition(
                    sprintf('app.%s.action.resolver.service', $server),
                    new DefinitionDecorator('app.action.resolver.service.abstract')
                );

            $container
                ->setDefinition(
                    sprintf('app.%s.object_mapper_resolver_and_extractor', $server),
                    new DefinitionDecorator('app.object_mapper_and_resolver_extractor.abstract')
                );

            $container
                ->setDefinition(
                    sprintf('api.%s.handler_builder', $server),
                    new DefinitionDecorator('app.handler_builder.abstract')
                )
                ->addMethodCall('addCallableResolver', [
                    new Reference(sprintf('app.%s.action.resolver.service', $server))
                ])
                ->addMethodCall('addActionLoader', [
                    new Reference(sprintf('app.%s.action.loader.annotated', $server))
                ])
                ->addMethodCall('setEventDispatcher', [
                    new Reference('event_dispatcher')
                ])
                ->addMethodCall('setParameterResolver', [
                    new Reference(sprintf('app.%s.object_mapper_resolver_and_extractor', $server))
                ]);
//
//            // Add handler to container
//            $container
//                ->setDefinition(
//                    sprintf('api.%s.handler', $server),
//                    new DefinitionDecorator('api.handler.abstract')
//                )
//                ->setFactory([
//                    new Reference(sprintf('api.%s.handler_builder', $server)),
//                    'buildHandler'
//                ])
//                ->addTag('api.handler', ['alias' => $server]);

            // Add server definition to container
            $container
                ->setDefinition(
                    sprintf('app.%s.server', $server),
                    new DefinitionDecorator('app.server.abstract')
                )
//                ->replaceArgument(0, new Reference(sprintf('app.%s.handler', $server)))
                ->addTag('app.server', ['alias' => $server]);

            // Add path and host to routing loader
            $container->getDefinition('app.routing_loader')
                ->addMethodCall('addServerPath', [
                    $server,
                    $info['path'],
                ]);

            $container->getDefinition('app.server_registry')
                ->addMethodCall('addServer', [
                    $server,
                    sprintf('app.%s.server', $server)
                ]);
        }
    }
}
