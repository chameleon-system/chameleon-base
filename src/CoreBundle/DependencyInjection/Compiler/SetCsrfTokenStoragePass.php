<?php

namespace ChameleonSystem\CoreBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use ChameleonSystem\CoreBundle\Security\AuthenticityToken\AuthenticityTokenStorage;

/**
 * Replace the token storage service of Symfony as that one uses "session" directly - which does not work with our own session handling.
 */
class SetCsrfTokenStoragePass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $tokenStorageDefinitionSymfony = $container->getDefinition('security.csrf.token_storage');
        $tokenStorageDefinitionChameleon = $container->getDefinition('chameleon_system_core.security.authenticity_token.authenticity_token_storage');

        $tokenStorageDefinitionSymfony->setClass($tokenStorageDefinitionChameleon->getClass());
        $tokenStorageDefinitionSymfony->setArguments($tokenStorageDefinitionChameleon->getArguments());
    }
}
