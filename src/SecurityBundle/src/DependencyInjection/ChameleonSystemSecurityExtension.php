<?php

namespace ChameleonSystem\SecurityBundle\DependencyInjection;

use ChameleonSystem\SecurityBundle\CmsGoogleLogin\GoogleAuthenticator;
use ChameleonSystem\SecurityBundle\CmsGoogleLogin\GoogleUserRegistrationService;
use ChameleonSystem\SecurityBundle\Controller\CmsLoginController;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
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

        $this->configureGoogleLogin($mergedConfig, $container);
        $this->configureTwoFactorLogin($mergedConfig, $container);
    }

    private function configureGoogleLogin(array $mergedConfig, ContainerBuilder $container): void
    {
        $enableGoogleLogin = $mergedConfig['google_login']['enabled'];

        $loginController = $container->getDefinition(CmsLoginController::class);
        $loginController->setArgument('$requestStack', new Reference('request_stack'));
        $loginController->setArgument('$enableGoogleLogin', $enableGoogleLogin);

        if (false === $enableGoogleLogin) {
            return;
        }
        $newUserConfig = $container->getDefinition(GoogleUserRegistrationService::class);
        $newUserConfig->setArgument(
            '$domainToBaseUserMapping',
            $mergedConfig['google_login']['domain_to_base_user_mapping'] ?? []
        );

        $authenticator = $container->getDefinition(GoogleAuthenticator::class);
        $authenticator->setArgument(
            '$allowedDomains',
            array_keys($mergedConfig['google_login']['domain_to_base_user_mapping'] ?? [])
        );
    }

    private function configureTwoFactorLogin(array $mergedConfig, ContainerBuilder $container): void
    {
        $enabled = $mergedConfig['two_factor']['enabled'] ?? false;
        $container->setParameter('chameleon_system_security.two_factor.enabled', $enabled);
    }
}
