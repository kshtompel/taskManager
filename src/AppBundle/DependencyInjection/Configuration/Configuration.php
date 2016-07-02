<?php

namespace AppBundle\DependencyInjection\Configuration;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('app');

        $this->addServersSection($rootNode);

        return $treeBuilder;
    }

    /**
     * Add servers section
     *
     * @param ArrayNodeDefinition $node
     */
    private function addServersSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('servers')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('path')
                                ->info('The path for this server')
                                ->isRequired()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }
}
