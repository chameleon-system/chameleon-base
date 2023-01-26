<?php

namespace ChameleonSystem\SecurityBundle\DependencyInjection;

use ChameleonSystem\SecurityBundle\CmsGoogleLogin\GoogleAuthenticator;
use ChameleonSystem\SecurityBundle\CmsGoogleLogin\GoogleUserRegistrationService;
use ChameleonSystem\SecurityBundle\Controller\CmsLoginController;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

class ChameleonSystemSecurityExtension extends ConfigurableExtension
{
    /**
     * @return void
     */
    public function loadInternal(array $mergedConfig, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../../config/'));
        $loader->load('services.xml');

        if (false === $mergedConfig['googleLogin']['enabled']) {
            return;
        }
        $loginController = $container->getDefinition(CmsLoginController::class);
        $loginController->setArgument('$enableGoogleLogin', true);
        $newUserConfig = $container->getDefinition(GoogleUserRegistrationService::class);
        $newUserConfig->setArgument('$allowedDomains', $mergedConfig['googleLogin']['domainToBaseUserMapping']);

        $authenticator = $container->getDefinition(GoogleAuthenticator::class);
        $authenticator->setArgument('$allowedDomains', $mergedConfig['googleLogin']['domainToBaseUserMapping']);
    }
}
