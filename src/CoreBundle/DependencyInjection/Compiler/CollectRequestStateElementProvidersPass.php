<?php

namespace ChameleonSystem\CoreBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class CollectRequestStateElementProvidersPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     *
     * @return void
     */
    public function process(ContainerBuilder $container)
    {
        $hashProvider = $container->getDefinition('chameleon_system_core.request_state_hash_provider');
        $elementProvider = array_keys(
            $container->findTaggedServiceIds('chameleon_system_core.request_state_element_provider')
        );

        $clearStateEvents = [];
        $elementProviderServiceDefinitions = [];
        foreach ($elementProvider as $serviceId) {
            $providerDefinition = $container->getDefinition($serviceId);
            $providerClass = $providerDefinition->getClass();
            $clearStateEvents = array_merge(
                $clearStateEvents,
                \call_user_func([$providerClass, 'getResetStateEvents'])
            );

            $elementProviderServiceDefinitions[] = $providerDefinition;
        }
        $hashProvider->replaceArgument(3, $elementProviderServiceDefinitions);

        $hashProviderCache = $container->getDefinition('chameleon_system_core.request_state_hash_provider_cache');
        $clearStateEvents = array_unique($clearStateEvents);
        foreach ($clearStateEvents as $clearStateEvent) {
            $hashProviderCache->addTag(
                'kernel.event_listener', ['event' => $clearStateEvent, 'method' => 'onStateDataChanged']
            );
        }
    }
}
