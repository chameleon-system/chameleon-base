<?php

namespace ChameleonSystem\BreadcrumbBundle\DependencyInjection;

use MyProject\Container;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\DependencyInjection\Reference;

class ChameleonSystemBreadcrumbCompilerPass implements CompilerPassInterface
{
    private const BREADCRUMB_GENERATOR_CHAIN_SERVICE_ID = 'chameleon_system_breadcrumb.provider.breadcrumb_generator_provider';
    private const BREADCRUMB_GENERATOR_CHAIN_TAG = 'chameleon_system_breadcrumb.generator.breadcrumb_generator';
    private const BREADCRUMB_GENERATOR_LIST_PARAM = '$breadcrumbGeneratorList';

    /**
     * {@inheritDoc}
     *
     * @throws ServiceNotFoundException
     * @throws InvalidArgumentException
     * @throws \InvalidArgumentException
     */
    public function process(ContainerBuilder $container): void
    {
        $this->setupBreadcrumbGeneratorProcessorChain($container);
    }

    private function setupBreadcrumbGeneratorProcessorChain(ContainerBuilder $container): void
    {
        if (false === $container->has(self::BREADCRUMB_GENERATOR_CHAIN_SERVICE_ID)) {
            return;
        }

        $providerList = [];
        $taggedServices = $container->findTaggedServiceIds(self::BREADCRUMB_GENERATOR_CHAIN_TAG);

        foreach ($taggedServices as $id => $tags) {
            foreach ($tags as $attributes) {
                if (false === isset($attributes['order'])) {
                    throw new \InvalidArgumentException(
                        sprintf(
                            'you need to provide a order when tagging a service with "%s"',
                            self::BREADCRUMB_GENERATOR_CHAIN_TAG
                        )
                    );
                }
                if (true === isset($providerList[$attributes['order']])) {
                    throw new \InvalidArgumentException(
                        sprintf(
                            'multiple services with same order priority tagged with "%s"',
                            self::BREADCRUMB_GENERATOR_CHAIN_TAG
                        )
                    );
                }
                $providerList[$attributes['order']] = new Reference($id);
            }
        }

        ksort($providerList);

        $def = $container->getDefinition(self::BREADCRUMB_GENERATOR_CHAIN_SERVICE_ID);
        $def->setArgument(self::BREADCRUMB_GENERATOR_LIST_PARAM, $providerList);
    }
}
