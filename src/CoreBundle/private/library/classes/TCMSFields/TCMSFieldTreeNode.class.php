<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * picks a node from a tree.
/**/
class TCMSFieldTreeNode extends TCMSField
{
    public function GetHTML()
    {
        $path = $this->_GetTreePath();
        $html = '<input type="hidden" id="'.TGlobalBase::OutHTML($this->name).'" name="'.TGlobalBase::OutHTML($this->name).'" value="'.TGlobalBase::OutHTML($this->data).'" />';
        $html .= '<div id="'.TGlobalBase::OutHTML($this->name).'_path">'.$path.'</div>';
        $html .= '<div class="cleardiv">&nbsp;</div>';
        $html .= TCMSRender::DrawButton(TGlobal::Translate('chameleon_system_core.field_tree_node.assign_node'), 'javascript:'.$this->_GetOpenWindowJS().';', URL_CMS.'/images/icons/page_navigation.gif');
        $html .= TCMSRender::DrawButton(TGlobal::Translate('chameleon_system_core.action.reset'), "javascript:ResetTreeNodeSelection('".TGlobalBase::OutHTML($this->name)."');", URL_CMS.'/images/icons/action_stop.gif');

        return $html;
    }

    public function GetReadOnly()
    {
        $html = $this->_GetHiddenField();
        if (!empty($this->data)) {
            $oNode = new TdbCmsTree();
            $oNode->Load($this->data);
            $html .= TGlobal::OutHTML($oNode->fieldPathcache);
        }

        return $html;
    }

    public function _GetTreePath()
    {
        $path = '';
        if (!empty($this->data)) {
            $oTreeNode = new TdbCmsTree();
            $oTreeNode->Load($this->data);
            $path = $oTreeNode->GetTreeNodePathAsBackendHTML();
        }

        return $path;
    }

    public function _GetOpenWindowJS()
    {
        $url = PATH_CMS_CONTROLLER.'?pagedef=treenodeselect&fieldName='.urlencode($this->name).'&id='.urlencode($this->data);
        $js = "CreateModalIFrameDialogCloseButton('".TGlobal::OutHTML($url)."')";

        return $js;
    }

    /**
     * return an array of all js, css, or other header includes that are required
     * in the cms for this field. each include should be in one line, and they
     * should always be typed the same way so that no includes are included mor than once.
     *
     * @return array
     */
    public function GetCMSHtmlHeadIncludes()
    {
        $aIncludes = array();
        $aIncludes[] = '<link href="'.TGlobal::GetPathTheme().'/css/module_CMSTreeNodeSelect.css" rel="stylesheet" type="text/css" />';

        return $aIncludes;
    }

    public function RenderFieldMethodsString()
    {
        $aMethodData = $this->GetFieldMethodBaseDataArray();

        $aMethodData['sMethodName'] = $this->GetFieldMethodName('PageURL');
        $aMethodData['aParameters'] = array(
            'bForceDomain' => array(
                'sType' => 'boolean',
                'description' => 'force include the portal domain (generate an absolute link)',
                'default' => 'false',
            ),
            'forcePageLanguage' => array(
                'sType' => 'boolean',
                'description' => 'force page language (if true, the language of the page is used; if false, the currently active language is used)',
                'default' => 'false',
            ),
        );
        $aMethodData['sReturnType'] = 'string|null';

        $oViewParser = new TViewParser();
        /** @var $oViewParser TViewParser */
        $oViewParser->bShowTemplatePathAsHTMLHint = false;
        $oViewParser->AddVarArray($aMethodData);

        $sMethodCode = $oViewParser->RenderObjectView('geturl', 'TCMSFields/TCMSFieldTreeNode');
        $oViewParser->AddVar('sMethodCode', $sMethodCode);

        return $oViewParser->RenderObjectView('method', 'TCMSFields/TCMSField');
    }

    /**
     * Get page tree connection date information as rendered html.
     *
     * @param string $sTreeId
     * @param string $sPageId
     *
     * @return string
     */
    protected function GetPageTreeConnectionDateInformationHTML($sTreeId, $sPageId)
    {
        $oCmsTree = TdbCmsTree::GetNewInstance();
        $oCmsTree->Load($sTreeId);
        $oCurrentActiveTreeConnection = $oCmsTree->GetActivePageTreeConnectionForTree();
        $oLocal = TCMSLocal::GetActive();
        $sPageTreeConnectionDateInformation = '';
        $sQuery = "SELECT * FROM `cms_tree_node`
                         WHERE `cms_tree_node`.`cms_tree_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($sTreeId)."'
                           AND `cms_tree_node`.`tbl` = 'cms_tpl_page'
                           AND `cms_tree_node`.`contid` = '".MySqlLegacySupport::getInstance()->real_escape_string($sPageId)."'";
        $oPageTreeConnectionList = TdbCmsTreeNodeList::GetList($sQuery);
        while ($oPageTreeConnection = $oPageTreeConnectionList->Next()) {
            if ($oCurrentActiveTreeConnection && $oPageTreeConnection->id == $oCurrentActiveTreeConnection->id) {
                $sPageTreeConnectionClass = 'dateinfo_inner_active';
            } else {
                $sPageTreeConnectionClass = 'dateinfo_inner_none_active';
            }

            $sStartDate = $oLocal->FormatDate($oPageTreeConnection->fieldStartDate);
            $sEndDate = $oLocal->FormatDate($oPageTreeConnection->fieldEndDate);

            $sDateText = '';
            if (!empty($sStartDate)) {
                $sDateText .= TGlobal::Translate('chameleon_system_core.field_tree_node.date_starting_on').' '.$sStartDate.' ';
            }
            if (!empty($sEndDate)) {
                $sDateText .= TGlobal::Translate('chameleon_system_core.field_tree_node.date_ending_on').' '.$sEndDate;
            }

            if (empty($sStartDate) && empty($sEndDate)) {
                $sDateText = TGlobal::Translate('chameleon_system_core.field_tree_node.date_unrestricted');
            }

            $sPageTreeConnectionDateInformation .= '<div class="dateinfo_inner '.$sPageTreeConnectionClass.'">'.$sDateText.'</div>';
        }

        return $sPageTreeConnectionDateInformation;
    }
}
