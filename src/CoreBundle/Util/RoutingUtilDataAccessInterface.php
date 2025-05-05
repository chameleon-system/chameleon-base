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

use Doctrine\DBAL\DBALException;

/**
 * A data access interface for the RoutingUtil class.
 */
interface RoutingUtilDataAccessInterface
{
    /**
     * Returns a list of navigation paths, along with some information on these paths (especially the pagedef ID).
     *
     * @return array
     */
    public function getNaviLookup(\TdbCmsPortal $portal, \TdbCmsLanguage $language);

    /**
     * Returns a list of tree nodes that are assigned to the passed $page in the given $language. The primary node is
     * always the first one in the returned list.
     *
     * @param \TdbCmsLanguage|null $language if null, the default language is used
     *
     * @return \TdbCmsTree[]
     */
    public function getPageTreeNodes(\TdbCmsTplPage $page, ?\TdbCmsLanguage $language = null);

    /**
     * Returns a list of all PageAssignments for the passed $portal in the given $language.
     *
     * @return string[]
     *
     * @throws DBALException
     */
    public function getAllPageAssignments(\TdbCmsPortal $portal, \TdbCmsLanguage $language);
}
