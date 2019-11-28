<?php
namespace ChameleonSystem\CoreBundle\Factory;

use ChameleonSystem\CoreBundle\DataModel\BackendTreeNodeDataModel;

class BackendTreeNodeFactory
{
    public function createTreeNodeDataModelFromTreeRecord(\TdbCmsTree $treeNode): BackendTreeNodeDataModel
    {
        $treeNodeDataModel = new BackendTreeNodeDataModel($treeNode->id, $treeNode->fieldName, $treeNode->sqlData['cmsident'], $this->getConnectedPageId($treeNode));
        return $treeNodeDataModel;
    }

    private function getConnectedPageId(\TdbCmsTree $treeNode): string
    {
        $treeNodeConnection = $treeNode->GetActivePageTreeConnectionForTree();
        if (false === $treeNodeConnection) {
            return "";
        }
        $connectedPage = $treeNodeConnection->GetFieldContid();
        if (null === $connectedPage) {
            return "";
        }
        return $connectedPage->id;
    }
}
