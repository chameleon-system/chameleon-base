<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\ServiceLocator;
use Symfony\Component\HttpFoundation\Request;

/**
 * @deprecated since 6.3.8 - use NavigationTreeSingleSelectWysiwyg instead
 */
class CMSTreeNodeSelectWYSIWYG extends CMSTreeNodeSelect
{
    public function Execute()
    {
        parent::Execute();
        $this->data['CKEditorFuncNum'] = $this->getRequest()->query->getInt('CKEditorFuncNum');

        return $this->data;
    }

    /**
     * @param string $fieldName
     * @param TCMSTreeNode $oNode
     *
     * @return string
     */
    protected function getOnClick($fieldName, $oNode)
    {
        $treeNodeConnection = $oNode->GetActivePageTreeConnectionForTree();
        if (false === $treeNodeConnection) {
            return '';
        }
        $page = $treeNodeConnection->GetFieldContid();
        if (null === $page) {
            return '';
        }
        $name = TGlobal::OutJS($oNode->GetName());
        $pageId = TGlobal::OutJS($page->id);
        $nodeId = TGlobal::OutJS($oNode->id);

        return "chooseTreeNode('{$pageId}','{$nodeId}','{$name}');";
    }

    /**
     * @return Request|null
     */
    private function getRequest()
    {
        return ServiceLocator::get('request_stack')->getCurrentRequest();
    }
}
