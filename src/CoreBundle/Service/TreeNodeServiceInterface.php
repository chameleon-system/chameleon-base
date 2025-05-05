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

use TdbCmsTreeNode;

/**
 * TreeNodeServiceInterface defines a service that offers basic operations on TdbCmsTreeNode objects.
 */
interface TreeNodeServiceInterface
{
    /**
     * Returns the tree node with the passed $treeNodeId.
     *
     * @param string $treeNodeId
     * @param string|null $languageId if null, the active language is used
     *
     * @return \TdbCmsTreeNode|null
     */
    public function getById($treeNodeId, $languageId = null);

    /**
     * Returns the tree node with the passed $treeId.
     *
     * @param string $treeId
     * @param string|null $languageId if null, the active language is used
     *
     * @return \TdbCmsTreeNode|null
     */
    public function getByTreeId($treeId, $languageId = null);
}
