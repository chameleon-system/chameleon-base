<?php

namespace ChameleonSystem\NewsletterBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{

    public function getConfigTreeBuilder(): TreeBuilder
    {
        $builder = new TreeBuilder('chameleon_system_newsletter');
        $root = $builder->getRootNode();

        $root
            ->children()
                ->arrayNode('import_newsletter_from_zip')
                    ->addDefaultsIfNotSet()
                        ->children()
                            ->booleanNode('enabled')
                                ->defaultFalse()
                            ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $builder;
    }

}