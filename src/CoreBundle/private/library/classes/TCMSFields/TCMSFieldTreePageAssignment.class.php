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
use ChameleonSystem\SecurityBundle\Service\SecurityHelperAccess;
use ChameleonSystem\SecurityBundle\Voter\CmsPermissionAttributeConstants;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * special field class for the tree. it shows the primary page attached to
 * the tree node. if no page is assigned, it will show a select box with master pagedefs instead
 * if one of these is selected, then a new page with that pagedef will be created.
 */
class TCMSFieldTreePageAssignment extends TCMSFieldVarchar
{
    /**
     * @var TCMSPagedef|null
     */
    protected $oAssignedPage;

    /**
     * {@inheritdoc}
     */
    public function GetHTML()
    {
        $sHTML = '<input type="hidden" id="'.TGlobal::OutHTML($this->name).'" name="'.TGlobal::OutHTML(
            $this->name
        ).'" value="'.TGlobal::OutHTML($this->data).'" />';
        $this->oAssignedPage = new TCMSPagedef();
        if ($this->hasLinkedPages()) {
            $translator = $this->getTranslator();
            $sHTML .= $translator->trans('chameleon_system_core.field_tree_page_assignment.already_linked_pages', [], ChameleonSystem\CoreBundle\i18n\TranslationConstants::DOMAIN_BACKEND);
        } else {
            $sHTML .= '<select name="'.TGlobal::OutHTML($this->name)."_pagedef\" class=\"form-control form-control-sm\">\n";
            $sHTML .= $this->GetPagedefSelectionOptions();
            $sHTML .= '</select>';
        }

        return $sHTML;
    }

    /**
     * @return bool
     */
    private function hasLinkedPages()
    {
        $oNode = new TCMSTreeNode();
        $oNode->Load($this->recordId);
        $oLinkedPages = $oNode->GetAllLinkedPages();

        return $oLinkedPages->Length() > 0;
    }

    /**
     * {@inheritdoc}
     */
    public function GetReadOnly()
    {
        return '';
    }

    /**
     * get the options for the pagedef select box.
     *
     * @return string
     */
    protected function GetPagedefSelectionOptions()
    {
        $sHTML = '<option value="">'.ServiceLocator::get('translator')->trans(
            'chameleon_system_core.field_tree_page_assignment.select_page_template'
        )."</option>\n";
        $oMasterPagedefs = $this->getPageDefListForTreePortal();
        $iSelectedId = $this->GetDefaultSelectedLayout();
        while ($oMasterPagedef = $oMasterPagedefs->Next()) {
            $sSelected = '';
            if ($iSelectedId === $oMasterPagedef->id) {
                $sSelected = 'selected="selected"';
            }
            $sHTML .= '<option value="'.TGlobal::OutHTML(
                $oMasterPagedef->id
            ).'" '.$sSelected.'>'.TGlobal::OutHTML($oMasterPagedef->GetName())."</option>\n";
        }

        return $sHTML;
    }

    /**
     * get portal restricted page def list.
     *
     * @return TdbCmsMasterPagedefList
     */
    protected function getPageDefListForTreePortal()
    {
        $tree = TdbCmsTree::GetNewInstance();
        $query = "SELECT `cms_master_pagedef`.*
                        FROM `cms_master_pagedef`
                   LEFT JOIN `cms_master_pagedef_cms_portal_mlt`
                          ON `cms_master_pagedef_cms_portal_mlt`.`source_id` = `cms_master_pagedef`.`id`
                   LEFT JOIN `cms_portal`
                          ON `cms_portal`.`id` = `cms_master_pagedef_cms_portal_mlt`.`target_id`
                       WHERE (`cms_master_pagedef`.`restrict_to_portals` = '1'
                             AND ( `cms_portal`.`id` IS NULL [{portalRestriction}]))
                          OR `cms_master_pagedef`.`restrict_to_portals` = '0'
                    ORDER BY `cms_master_pagedef`.`position` ASC";
        $portalQueryRestriction = '';
        if (true === $tree->Load($this->recordId)) {
            $portal = $tree->GetNodePortal();
            if (null !== $portal) {
                $portalQueryRestriction = "OR `cms_portal`.`id` = '".$portal->id."'";
            }
        }
        $query = str_replace('[{portalRestriction}]', $portalQueryRestriction, $query);

        return TdbCmsMasterPagedefList::GetList($query);
    }

    protected function GetDefaultSelectedLayout()
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function PostSaveHook($sRecordId)
    {
        // check to see if a pagedef for this field is set... if it is, and no page is assigned
        // to the node (either primary or secondary) then we create it using the master pagedef
        // passed but only if "external link" is not set
        $this->oAssignedPage = new TCMSPagedef();
        if (true === $this->oAssignedPage->LoadFromField('primary_tree_id_hidden', $sRecordId)) {
            return;
        }

        $sMasterPagedefId = null;
        if (array_key_exists($this->name.'_pagedef', $this->oTableRow->sqlData)) {
            $sMasterPagedefId = $this->oTableRow->sqlData[$this->name.'_pagedef'];
        }

        if (empty($sMasterPagedefId)) {
            return;
        }

        /** @var SecurityHelperAccess $securityHelper */
        $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);
        if (false === $securityHelper->isGranted(CmsPermissionAttributeConstants::TABLE_EDITOR_NEW, 'cms_tpl_page')) {
            return;
        }

        $this->createPageForNode($sRecordId, $sMasterPagedefId);
    }

    /**
     * @param string $treeNodeId
     * @param string $masterPagedefId
     */
    protected function createPageForNode($treeNodeId, $masterPagedefId)
    {
        $connectedPageTableConfig = $this->oAssignedPage->GetTableConf();
        $treeNodeRecord = TdbCmsTree::GetNewInstance();
        $treeNodeRecord->Load($treeNodeId);
        $nodePortal = $treeNodeRecord->GetNodePortal();
        $nodePortalId = '';
        if (null !== $nodePortal) {
            $nodePortalId = $nodePortal->id;
        }
        /** @var SecurityHelperAccess $securityHelper */
        $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);
        $tableManager = new TCMSTableEditorManager();
        $tableManager->Init($connectedPageTableConfig->id, null);
        $tableManager->Insert();
        $defaultData = [
            'id' => $tableManager->sId,
            'name' => $this->oTableRow->sqlData['name'],
            'cms_portal_id' => $nodePortalId,
            'primary_tree_id_hidden' => $treeNodeId,
            'cms_user_id' => $securityHelper->getUser()?->getId(),
        ];

        $additionalDefaultData = $this->getDefaultPageData();
        $defaultData = array_merge($defaultData, $additionalDefaultData);

        $tableManager->Save($defaultData);
        $this->oAssignedPage->Load($tableManager->sId);
        $this->oAssignedPage->ChangeMasterPagedef($masterPagedefId);
    }

    /**
     * @return array
     */
    protected function getDefaultPageData()
    {
        return [];
    }

    /**
     * return false to prevent triggers such as delete from acting on this field (it has its own handler).
     *
     * @return string
     */
    public function GetMatchingParentFieldName()
    {
        return false;
    }

    /**
     * @return TranslatorInterface
     */
    private function getTranslator()
    {
        return ServiceLocator::get('translator');
    }
}
