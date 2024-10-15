<?php

declare(strict_types=1);

namespace Robole\SuluAITranslatorBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('sulu_ai_translator');

        $rootNode = $treeBuilder->getRootNode();
        $rootNode
            ->children()
                ->scalarNode('deepl_api_key')
                    ->end()
                ->arrayNode('locale_mapping')
                    ->useAttributeAsKey('name')
                    ->scalarPrototype()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
