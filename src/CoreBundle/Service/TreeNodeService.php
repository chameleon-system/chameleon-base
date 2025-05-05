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

use ChameleonSystem\CoreBundle\DataAccess\DataAccessInterface;

class TreeNodeService implements TreeNodeServiceInterface
{
    /**
     * @var DataAccessInterface
     */
    private $dataAccess;

    public function __construct(DataAccessInterface $dataAccess)
    {
        $this->dataAccess = $dataAccess;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($treeNodeId, $languageId = null)
    {
        $treeNodeList = $this->dataAccess->loadAll($languageId);
        if (!isset($treeNodeList[$treeNodeId])) {
            return null;
        }

        return $treeNodeList[$treeNodeId];
    }

    /**
     * {@inheritdoc}
     */
    public function getByTreeId($treeId, $languageId = null)
    {
        /** @var \TdbCmsTreeNode[] $treeNodeList */
        $treeNodeList = $this->dataAccess->loadAll($languageId);
        foreach ($treeNodeList as $treeNode) {
            if ($treeNode->fieldCmsTreeId === $treeId) {
                return $treeNode;
            }
        }

        return null;
    }
}
