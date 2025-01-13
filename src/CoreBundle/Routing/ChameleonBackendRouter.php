<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Routing;

use ChameleonSystem\CoreBundle\Service\LanguageServiceInterface;
use ChameleonSystem\CoreBundle\ServiceLocator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Matcher\RequestMatcherInterface;

class ChameleonBackendRouter extends ChameleonBaseRouter implements RequestMatcherInterface
{
    protected function generateCacheDirPath(string $baseCacheDir): string
    {
        return sprintf('%s/backend', $baseCacheDir);
    }

    /**
     * {@inheritdoc}
     */
    protected function getRouterConfig()
    {
        $configArray = [];

        // add backend route
        $configArray[] = [
            'name' => 'cms_tpl_page',
            'resource' => '@ChameleonSystemCoreBundle/Resources/config/route_backend.yml',
            'type' => 'yaml',
        ];

        return $configArray;
    }

    /**
     * {@inheritdoc}
     */
    public function match(string $pathinfo): array
    {
        $match = parent::match($pathinfo);
        $this->addLocaleToMatch($match);

        return $match;
    }

    /**
     * {@inheritdoc}
     */
    public function matchRequest(Request $request): array
    {
        $match = parent::matchRequest($request);
        $this->addLocaleToMatch($match);

        return $match;
    }

    private function addLocaleToMatch(array &$match): void
    {
        if (isset($match['_locale'])) {
            return;
        }
        $language = $this->getLanguageService()->getActiveLanguage();
        if (null !== $language) {
            $match['_locale'] = $language->fieldIso6391;
        }
    }

    /**
     * Avoid BC break - to be changed to use real injection (even constructor pseudo-injection won't work because an
     * object of this class is instantiated before the static container is ready).
     */
    private function getLanguageService(): LanguageServiceInterface
    {
        return ServiceLocator::get('chameleon_system_core.language_service');
    }
}
