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
use TdbCmsTree;

/**
 * TreeServiceInterface defines a service that offers basic operations on TdbCmsTree objects.
 */
interface TreeServiceInterface
{
    /**
     * Returns a tree for the passed $treeId.
     *
     * @param string $treeId
     * @param string|null $languageId if null, the active language is used
     *
     * @return \TdbCmsTree|null
     */
    public function getById($treeId, $languageId = null);

    /**
     * Returns all children of the node with the passed $treeId.
     *
     * @param string $treeId
     * @param bool $includeHidden if true, hidden children will be included
     * @param string|null $languageId if null, the active language is used
     *
     * @return \TIterator list of TdbCmsTree objects
     */
    public function getChildren($treeId, $includeHidden = false, $languageId = null);

    /**
     * Returns a URL to the page that is assigned to the passed tree for the passed portal
     * in the passed language. Note that this link might be absolute if it requires HTTPS access.
     *
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
    public function getLinkToPageForTreeRelative(\TdbCmsTree $tree, array $parameters = [], ?\TdbCmsLanguage $language = null);

    /**
     * Returns an absolute URL to the page that is assigned to the passed tree for the passed
     * portal in the passed language.
     *
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
    public function getLinkToPageForTreeAbsolute(\TdbCmsTree $tree, array $parameters = [], ?\TdbCmsLanguage $language = null, $forceSecure = false);

    /**
     * return the page ids for which the no follow rule should be inverted.
     *
     * @param string $treeId
     *
     * @return array
     */
    public function getInvertedNoFollowRulePageIds($treeId);
}
