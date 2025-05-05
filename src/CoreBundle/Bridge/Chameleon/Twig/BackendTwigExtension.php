<?php

namespace ChameleonSystem\CoreBundle\Bridge\Chameleon\Twig;

use ChameleonSystem\SecurityBundle\Service\SecurityHelperAccess;
use ChameleonSystem\SecurityBundle\Voter\CmsUserRoleConstants;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class BackendTwigExtension extends AbstractExtension
{
    public function __construct(
        private readonly SecurityHelperAccess $securityHelper,
        private readonly \TModuleLoader $moduleLoader
    ) {
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('cms_version', [$this, 'getCmsVersion']),
            new TwigFunction('server_address', [$this, 'getServerAddress']),
            new TwigFunction('cms_user_logged_in', [$this, 'isCmsBackendUserLoggedIn']),
            new TwigFunction('backend_module', [$this, 'module'], ['is_safe' => ['html']]),
            new TwigFunction('static_url', ['TGlobal', 'GetStaticURL']),
            new TwigFunction('static_url_to_weblib', ['TGlobal', 'GetStaticURLToWebLib']),
            new TwigFunction('path_theme', ['TGlobal', 'GetPathTheme']),
        ];
    }

    public function getCmsVersion(): string
    {
        $composerLockFile = PATH_PROJECT_BASE.'composer.lock';
        $packageName = 'chameleon-system/chameleon-base';

        if (file_exists($composerLockFile)) {
            $composerData = json_decode(file_get_contents($composerLockFile), true);

            foreach ($composerData['packages'] as $package) {
                if ($package['name'] === $packageName) {
                    return $package['version'];
                }
            }
        }

        return 'Version not found';
    }

    public function getServerAddress(): string
    {
        return $_SERVER['SERVER_ADDR'] ?? 'Unknown';
    }

    public function isCmsBackendUserLoggedIn(): bool
    {
        return $this->securityHelper->isGranted(CmsUserRoleConstants::CMS_USER);
    }

    public function module(string $name, bool $returnString = true, ?string $customWrapping = null, bool $allowAutoWrap = false): ?string
    {
        return $this->moduleLoader->GetModule($name, $returnString, $customWrapping, $allowAutoWrap);
    }
}
