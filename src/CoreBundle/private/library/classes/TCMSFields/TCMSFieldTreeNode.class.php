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
        $html .= TCMSRender::DrawButton(TGlobal::Translate('chameleon_system_core.field_tree_node.assign_node'), 'javascript:'.$this->_GetOpenWindowJS().';', 'fas fa-check');
        $html .= TCMSRender::DrawButton(TGlobal::Translate('chameleon_system_core.action.reset'), "javascript:ResetTreeNodeSelection('".TGlobalBase::OutHTML($this->name)."');", 'fas fa-undo');

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
     * @deprecated since 6.3.0 - date related tree nodes are not working anymore, functionality will be removed.
     *
     * Get page tree connection date information as rendered html.
     *
     * @param string $sTreeId
     * @param string $sPageId
     *
     * @return string
     */
    protected function GetPageTreeConnectionDateInformationHTML($sTreeId, $sPageId)
    {
        return '';
    }
}
