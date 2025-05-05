<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\DataAccess;

interface DataAccessCmsTreeInterface
{
    /**
     * Loads all entries of the underlying data model.
     *
     * @param string|null $languageId if null, the currently active language is used
     *
     * @return \TdbCmsTree[]
     */
    public function loadAll($languageId = null);

    /**
     * an array of all no follow invert rule page ids. key of the array is the tree_id.
     *
     * @return array
     */
    public function getAllInvertedNoFollowRulePageIds();

    /**
     * return the page ids for which the no follow rule should be inverted.
     *
     * @param string $cmsTreeId
     *
     * @return array
     */
    public function getInvertedNoFollowRulePageIds($cmsTreeId);
}
