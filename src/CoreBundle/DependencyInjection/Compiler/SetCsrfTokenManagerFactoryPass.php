<?php

namespace ChameleonSystem\CoreBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class SetCsrfTokenManagerFactoryPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     *
     * @return void
     */
    public function process(ContainerBuilder $container)
    {
        $tokenManagerDefinition = $container->getDefinition('security.csrf.token_manager');
        $factoryDefinition = $container->getDefinition('chameleon_system_core.security.authenticity_token.csrf_token_manager_factory');
        $tokenManagerDefinition->setFactory([$factoryDefinition, 'createCsrfTokenManager']);
    }
}
