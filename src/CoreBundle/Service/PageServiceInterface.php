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

use Symfony\Component\Routing\Exception\RouteNotFoundException;
use TdbCmsTplPage;

/**
 * PageServiceInterface defines a service that offers basic operations on TdbCmsTplPage objects.
 */
interface PageServiceInterface
{
    /**
     * Returns the page with the passed $pageId in the passed language.
     *
     * @param string $pageId
     * @param string|null $languageId if null, the active language is used
     *
     * @return \TdbCmsTplPage|null
     */
    public function getById($pageId, $languageId = null);

    /**
     * Returns the page assigned to the passed $treeId in the passed language.
     *
     * @param string $treeId
     * @param string|null $languageId if null, the active language is used
     *
     * @return \TdbCmsTplPage|null
     */
    public function getByTreeId($treeId, $languageId = null);

    /**
     * Returns a URL to the page with the passed $pageId for the passed portal in the passed language. If a $treeNodeId
     * is passed, the path to that node is used. If the $treeNodeId is null, the primary node is used. Note that this
     * link might be absolute if it requires HTTPS access.
     *
     * @param string $pageId
     * @param array $parameters an array of key-value parameters to add to the URL. You may also pass the 'domain' parameter
     *                          to generate the URL for a domain other than the default. When doing this, ask an implementation
     *                          of ChameleonSystem\CoreBundle\Util\RoutingUtilInterface::getHostRequirementPlaceholder()
     *                          for the exact name of the domain parameter. The domain parameter has no effect if the
     *                          resulting URL is relative.
     * @param \TdbCmsLanguage|null $language if null, the active language is used
     *
     * @return string
     *
     * @throws RouteNotFoundException
     */
    public function getLinkToPageRelative($pageId, array $parameters = [], ?\TdbCmsLanguage $language = null);

    /**
     * @see getLinkToPageRelative()
     *
     * @return string
     *
     * @throws RouteNotFoundException
     */
    public function getLinkToPageObjectRelative(\TdbCmsTplPage $page, array $parameters = [], ?\TdbCmsLanguage $language = null);

    /**
     * Returns an absolute URL to the page with the passed $pageId for the passed portal in the passed language. If a $treeNodeId
     * is passed, the path to that node is used. If the $treeNodeId is null, the primary node is used.
     *
     * @param string $pageId
     * @param array $parameters an array of key-value parameters to add to the URL. You may also pass the 'domain' parameter
     *                          to generate the URL for a domain other than the default. When doing this, ask an implementation
     *                          of ChameleonSystem\CoreBundle\Util\RoutingUtilInterface::getHostRequirementPlaceholder()
     *                          for the exact name of the domain parameter.
     * @param \TdbCmsLanguage|null $language if null, the active language is used
     * @param bool $forceSecure if true, the resulting URL will be an HTTPS URL in any case
     *
     * @return string
     *
     * @throws RouteNotFoundException
     */
    public function getLinkToPageAbsolute($pageId, array $parameters = [], ?\TdbCmsLanguage $language = null, $forceSecure = false);

    /**
     * @see getLinkToPageAbsolute()
     *
     * @param bool $forceSecure
     *
     * @return string
     *
     * @throws RouteNotFoundException
     */
    public function getLinkToPageObjectAbsolute(\TdbCmsTplPage $page, array $parameters = [], ?\TdbCmsLanguage $language = null, $forceSecure = false);

    /**
     * Returns a URL to the portal home page for the passed portal in the passed language. Note that this
     * link might be absolute if the portal home requires HTTPS access.
     *
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
     * @throws RouteNotFoundException
     */
    public function getLinkToPortalHomePageRelative(array $parameters = [], ?\TdbCmsPortal $portal = null, ?\TdbCmsLanguage $language = null);

    /**
     * Returns an absolute URL to the portal home page for the passed portal in the passed language.
     *
     * @param array $parameters an array of key-value parameters to add to the URL. You may also pass the 'domain' parameter
     *                          to generate the URL for a domain other than the default. When doing this, ask an implementation
     *                          of ChameleonSystem\CoreBundle\Util\RoutingUtilInterface::getHostRequirementPlaceholder()
     *                          for the exact name of the domain parameter. The domain parameter has no effect if the
     *                          resulting URL is relative.
     * @param \TdbCmsPortal|null $portal if null, the active portal is used
     * @param \TdbCmsLanguage|null $language if null, the active language is used
     * @param bool $forceSecure if true, the resulting URL will be an HTTPS URL in any case
     *
     * @return string
     *
     * @throws RouteNotFoundException
     */
    public function getLinkToPortalHomePageAbsolute(array $parameters = [], ?\TdbCmsPortal $portal = null, ?\TdbCmsLanguage $language = null, $forceSecure = false);
}
