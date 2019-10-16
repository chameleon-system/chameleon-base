<?php
namespace ChameleonSystem\CoreBundle\Factory;

use ChameleonSystem\CoreBundle\DataModel\BackendTreeNodeDataModel;

class BackendTreeNodeFactory
{
    public function createTreeNodeDataModelFromTreeRecord(\TdbCmsTree $treeNode, string $childrenAjaxUrl)
    {
        $treeNodeDataModel = new BackendTreeNodeDataModel($treeNode->id, $treeNode->fieldName, $treeNode->sqlData['cmsident'], $childrenAjaxUrl);

        return $treeNodeDataModel;
    }
}
