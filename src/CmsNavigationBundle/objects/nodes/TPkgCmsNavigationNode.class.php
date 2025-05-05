<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\Service\TreeServiceInterface;

/**
 * @extends AbstractPkgCmsNavigationNode<TdbCmsTree>
 */
class TPkgCmsNavigationNode extends AbstractPkgCmsNavigationNode
{
    /**
     * {@inheritdoc}
     */
    public function getAChildren()
    {
        if (true === $this->bDisableSubmenu) {
            return null;
        }
        if (null === $this->aChildren) {
            if (null !== $this->getNodeCopy()) {
                $this->aChildren = [];
                $oChildren = $this->getNodeCopy()->GetChildren();
                while ($oChild = $oChildren->Next()) {
                    /** @psalm-var class-string<self> $sClass */
                    $sClass = get_class($this);
                    $oNaviNode = new $sClass();

                    $oNaviNode->iLevel = $this->iLevel + 1;
                    if (true === $oNaviNode->loadFromNode($oChild)) {
                        $this->aChildren[] = $oNaviNode;
                    }
                }
            }
        }

        return $this->aChildren;
    }

    /**
     * {@inheritdoc}
     */
    public function load($sId)
    {
        $node = $this->getTreeService()->getById($sId);
        if (null === $node) {
            return false;
        }

        return $this->loadFromNode($node);
    }

    /**
     * {@inheritdoc}
     */
    public function loadFromNode($oNode)
    {
        if (false === $oNode->IsActive() || false === $oNode->AllowAccessByCurrentUser()) {
            return false;
        }
        $this->setNodeCopy($oNode);
        $this->setFromCmsTreeNode($oNode);

        return true;
    }

    /**
     * @return void
     */
    private function setFromCmsTreeNode(TdbCmsTree $oNode)
    {
        $this->sLink = $oNode->getLink();
        $this->sTitle = $oNode->GetName();

        $aLinkAttributes = $oNode->GetLinkAttributes();

        $this->sSeoTitle = isset($aLinkAttributes['title']) ? ($aLinkAttributes['title']) : false;
        $this->sRel = isset($aLinkAttributes['rel']) ? ($aLinkAttributes['rel']) : false;
        $this->sAccessKey = isset($aLinkAttributes['accesskey']) ? ($aLinkAttributes['accesskey']) : false;
        $this->sTarget = isset($aLinkAttributes['target']) ? ($aLinkAttributes['target']) : false;
        $this->sNavigationIconURL = $this->getNodeIconURL();
        $this->sCssClass = $oNode->fieldCssClasses;
    }

    /**
     * {@inheritdoc}
     */
    public function getBIsActive()
    {
        if (null === $this->bIsActive) {
            $this->bIsActive = false;
            if (null !== $this->getNodeCopy()) {
                $this->bIsActive = $this->getNodeCopy()->IsActiveNode();
            }
        }

        return $this->bIsActive;
    }

    /**
     * {@inheritdoc}
     */
    public function getBIsExpanded()
    {
        if (null === $this->bIsExpanded) {
            $this->bIsExpanded = $this->getBIsActive();
            if (null !== $this->getNodeCopy()) {
                $this->bIsExpanded = ($this->bIsExpanded || $this->getNodeCopy()->IsInBreadcrumb());
            }
        }

        return $this->bIsExpanded;
    }

    /**
     * {@inheritdoc}
     */
    public function getNodeIconURL()
    {
        $sURL = null;
        $oNode = $this->getNodeCopy();
        $oImage = $oNode->GetImage(0, 'navi_icon_cms_media_id', $this->dummyImagesAllowed());
        if ($oImage) {
            $sURL = $oImage->GetRelativeURL();
            $this->sNavigationIconId = $oImage->id;
        }

        return $sURL;
    }

    /**
     * @return TreeServiceInterface
     */
    private function getTreeService()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.tree_service');
    }
}
