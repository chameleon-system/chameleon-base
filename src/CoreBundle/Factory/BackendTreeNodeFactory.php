<?php
namespace ChameleonSystem\CoreBundle\Factory;

use ChameleonSystem\CoreBundle\DataModel\BackendTreeNodeDataModel;

class BackendTreeNodeFactory
{
    public function createTreeNodeDataModelFromTreeRecord(\TdbCmsTree $treeNode): BackendTreeNodeDataModel
    {
        $treeNodeDataModel = new BackendTreeNodeDataModel($treeNode->id, $treeNode->fieldName, $treeNode->sqlData['cmsident']);

        return $treeNodeDataModel;
    }
}
