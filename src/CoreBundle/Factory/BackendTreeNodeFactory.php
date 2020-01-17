<?php
/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Factory;

use ChameleonSystem\CoreBundle\DataModel\BackendTreeNodeDataModel;

class BackendTreeNodeFactory
{
    public function createTreeNodeDataModelFromTreeRecord(\TdbCmsTree $treeNode): BackendTreeNodeDataModel
    {
        return new BackendTreeNodeDataModel(
            $treeNode->id,
            $treeNode->fieldName,
            $treeNode->sqlData['cmsident'],
            $this->getConnectedPageId($treeNode)
        );
    }

    private function getConnectedPageId(\TdbCmsTree $treeNode): string
    {
        $treeNodeConnection = $treeNode->GetActivePageTreeConnectionForTree();
        if (false === $treeNodeConnection) {
            return '';
        }
        $connectedPage = $treeNodeConnection->GetFieldContid();
        if (null === $connectedPage) {
            return '';
        }

        return $connectedPage->id;
    }
}
