<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Util;

use ChameleonSystem\CoreBundle\DataModel\Routing\PagePath;
use Symfony\Component\Routing\Route;

interface RoutingUtilInterface
{
    /**
     * Returns the URL of a page assigned to the passed tree node, or null if there is no assigned page.
     * This method is only intended for use during route generation or other tasks performed at compile time
     * as it may incorporate slow backend access. During runtime, use PortalAndLanguageAwareRouterInterface::generateWithPrefixes().
     *
     * @return string|null
     */
    public function getLinkForTreeNode(\TdbCmsTree $tree, \TdbCmsLanguage $language);

    /**
     * Returns the placeholder for domains that needs to be defined in each route.
     *
     * @return string
     */
    public function getHostRequirementPlaceholder();

    /**
     * Returns the domain requirements part of a route (all domains for the given portal).
     *
     * @param bool $secure
     *
     * @return string
     */
    public function getDomainRequirement(\TdbCmsPortal $portal, \TdbCmsLanguage $language, $secure);

    /**
     * Returns a list of routes for the passed $portal in the given $language. The list indexes are the page IDs, the
     * values are corresponding PagePath objects.
     *
     * @return PagePath[]
     */
    public function getAllPageRoutes(\TdbCmsPortal $portal, \TdbCmsLanguage $language);

    /**
     * Normalizes a route path in such a way that prefixes and suffixes are removed. This method may not be as
     * universal as you desire, for it relies on the system configuration to determine which transformations to perform.
     * Mostly this method will be used for page routes.
     *
     * @param string $path
     *
     * @return string
     */
    public function normalizeRoutePath($path, \TdbCmsPortal $portal);
}
