<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\Service\ActivePageServiceInterface;

/**
 * show only the active page and its first sublevel.
 * /**/
class TCCustomNavigationOneLevelActivePage extends TCCustomNavigationOnlyOneLevel
{
    /**
     * overwrite load to use the active page node instead of the passed rootNodeId.
     *
     * @param int $rootNodeId - root navi id. we will overwrite this
     * @param int $iCurrentPage - current page id
     */
    public function Load($rootNodeId, $iCurrentPage)
    {
        $oActiveNode = $this->getActivePageService()->getActivePage()->GetTreeNode();

        // If the node has children, then we have our root node. If not, we use its parent instead.
        // If the parent happens to be above $rootNodeId, then use rootNodeId.
        // If the active page node is not below the rootNodeId, then we also use the rootNodeId.
        if (TCMSTreeNode::IsBelowNode($rootNodeId, $oActiveNode->id)) {
            $oNodeChildren = $oActiveNode->GetChildren();
            if ($oNodeChildren->Length() > 0) {
                $rootNodeId = $oActiveNode->id;
            } elseif (TCMSTreeNode::IsBelowNode($rootNodeId, $oActiveNode->sqlData['parent_id'])) {
                $rootNodeId = $oActiveNode->sqlData['parent_id'];
            }
        } elseif (TCMSTreeNode::IsBelowNode($rootNodeId, $oActiveNode->sqlData['parent_id'])) {
            $rootNodeId = $oActiveNode->sqlData['parent_id'];
        }

        parent::Load($rootNodeId, $iCurrentPage);
    }

    /**
     * we add the root node to the menu as an h2.
     *
     * @param string $sMenu - the complete rendered menu
     *
     * @return string - the rendered menu
     */
    protected function PostMenuRender($sMenu)
    {
        $sMenu = parent::PostMenuRender($sMenu);
        $sMenu = '<h1>'.TGlobal::OutHTML($this->oRootNode->GetName())."</h1>\n".$sMenu;

        return $sMenu;
    }

    /**
     * @return ActivePageServiceInterface
     */
    private function getActivePageService()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.active_page_service');
    }
}
