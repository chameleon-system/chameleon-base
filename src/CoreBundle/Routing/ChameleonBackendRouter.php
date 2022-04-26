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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Matcher\RequestMatcherInterface;

class ChameleonBackendRouter extends ChameleonBaseRouter implements RequestMatcherInterface
{
    /**
     * {@inheritdoc}
     */
    protected function getMatcherCacheClassName()
    {
        return 'chameleonBackend'.ucfirst($this->environment).'UrlMatcher';
    }

    /**
     * {@inheritdoc}
     */
    protected function getGeneratorCacheClassName()
    {
        return 'chameleonBackend'.ucfirst($this->environment).'UrlGenerator';
    }

    /**
     * {@inheritdoc}
     */
    protected function getRouterConfig()
    {
        $configArray = array();

        // add backend route
        $configArray[] = array(
            'name' => 'cms_tpl_page',
            'resource' => '@ChameleonSystemCoreBundle/Resources/config/route_backend.yml',
            'type' => 'yaml',
        );

        return $configArray;
    }

    /**
     * {@inheritdoc}
     */
    public function match($pathinfo)
    {
        $match = parent::match($pathinfo);
        if (is_array($match)) {
            $this->addLocaleToMatch($match);
        }

        return $match;
    }

    /**
     * {@inheritdoc}
     */
    public function matchRequest(Request $request)
    {
        $match = parent::matchRequest($request);
        if (is_array($match)) {
            $this->addLocaleToMatch($match);
        }

        return $match;
    }

    /**
     * @param array $match
     *
     * @return void
     */
    private function addLocaleToMatch(array &$match)
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
     *
     * @return LanguageServiceInterface
     */
    private function getLanguageService()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.language_service');
    }
}
