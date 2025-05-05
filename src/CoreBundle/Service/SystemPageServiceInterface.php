<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Service;

use ChameleonSystem\CoreBundle\DataModel\PageDataModel;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

/**
 * SystemPageServiceInterface defines a service that provides methods to get information on system pages.
 */
interface SystemPageServiceInterface
{
    /**
     * Returns a complete list of system pages for the passed portal in the passed language.
     *
     * @return \TdbCmsPortalSystemPage[]
     */
    public function getSystemPageList(\TdbCmsPortal $portal, \TdbCmsLanguage $language);

    /**
     * Returns a system page with the passed internal name for the passed portal in the passed language, or null
     * if no system page matching these requirements exists.
     *
     * @param string $systemPageNameInternal
     * @param \TdbCmsPortal|null $portal if null, the active portal is used
     * @param \TdbCmsLanguage|null $language if null, the active language is used
     *
     * @return \TdbCmsPortalSystemPage|null
     */
    public function getSystemPage(
        $systemPageNameInternal,
        ?\TdbCmsPortal $portal = null,
        ?\TdbCmsLanguage $language = null
    );

    /**
     * Returns a URL to the system page with the passed internal name for the passed portal in the passed language.
     * Note that this link might be absolute if it requires HTTPS access.
     *
     * @param string $systemPageNameInternal
     * @param array $parameters an array of key-value parameters to add to the URL. You may also pass the 'domain' parameter
     *                          to generate the URL for a domain other than the default. When doing this, ask an implementation
     *                          of ChameleonSystem\CoreBundle\Util\RoutingUtilInterface::getHostRequirementPlaceholder()
     *                          for the exact name of the domain parameter. The domain parameter has no effect if the
     *                          resulting URL is relative.
     * @param \TdbCmsPortal|null $portal if null, the active portal is used
     * @param \TdbCmsLanguage|null $language if null, the active language is used
     *
     * @return string
     *
     * @throws RouteNotFoundException if there is no system page with the passed name or if no page is assigned
     */
    public function getLinkToSystemPageRelative(
        $systemPageNameInternal,
        array $parameters = [],
        ?\TdbCmsPortal $portal = null,
        ?\TdbCmsLanguage $language = null
    );

    /**
     * Returns an absolute URL to the system page with the passed internal name for the passed portal in the passed language.
     *
     * @param string $systemPageNameInternal
     * @param array $parameters an array of key-value parameters to add to the URL. You may also pass the 'domain' parameter
     *                          to generate the URL for a domain other than the default. When doing this, ask an implementation
     *                          of ChameleonSystem\CoreBundle\Util\RoutingUtilInterface::getHostRequirementPlaceholder()
     *                          for the exact name of the domain parameter.
     * @param \TdbCmsPortal|null $portal if null, the active portal is used
     * @param \TdbCmsLanguage|null $language if null, the active language is used
     * @param bool $forceSecure if true, the resulting URL will be an HTTPS URL in any case
     *
     * @return string
     *
     * @throws RouteNotFoundException if there is no system page with the passed name or if no page is assigned
     */
    public function getLinkToSystemPageAbsolute(
        $systemPageNameInternal,
        array $parameters = [],
        ?\TdbCmsPortal $portal = null,
        ?\TdbCmsLanguage $language = null,
        $forceSecure = false
    );

    /**
     * Returns the tree node connected with the system page $systemPageNameInternal.
     *
     * @param \TdbCmsPortal|null $portal if null, the active portal is used
     * @param \TdbCmsLanguage|null $language if null, the active language is used
     *
     * @throws RouteNotFoundException if there is no system page with the passed name or if no page is assigned
     */
    public function getSystemPageTree(string $systemPageNameInternal, ?\TdbCmsPortal $portal = null, ?\TdbCmsLanguage $language = null): ?\TdbCmsTree;

    /**
     * Returns an absolute URL to the system page with the passed internal name for the passed portal in the passed language.
     *
     * @param array $parameters an array of key-value parameters to add to the URL. You may also pass the 'domain' parameter
     *                          to generate the URL for a domain other than the default. When doing this, ask an implementation
     *                          of ChameleonSystem\CoreBundle\Util\RoutingUtilInterface::getHostRequirementPlaceholder()
     *                          for the exact name of the domain parameter.
     * @param \TdbCmsPortal|null $portal if null, the active portal is used
     * @param \TdbCmsLanguage|null $language if null, the active language is used
     *
     * @return string
     *
     * @throws RouteNotFoundException if there is no system page with the passed name or if no page is assigned
     */
    public function getPageDataModel(string $systemPageNameInternal, array $parameters = [], ?\TdbCmsPortal $portal = null, ?\TdbCmsLanguage $language = null): ?PageDataModel;
}
