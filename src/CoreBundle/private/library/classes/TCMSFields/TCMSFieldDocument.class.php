<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TCMSFieldDocument extends TCMSFieldLookup
{
    public function GetHTML()
    {
        $this->oTableConf = &$this->oTableRow->GetTableConf();

        $html = '<input type="hidden" id="'.TGlobal::OutHTML($this->name).'" name="'.TGlobal::OutHTML($this->name).'" value="'.TGlobal::OutHTML($this->data).'" />
      <div>';

        $currentFile = TGlobal::Translate('chameleon_system_core.field_document.nothing_selected');
        if (!empty($this->data)) {
            $oCmsDocument = TdbCmsDocument::GetNewInstance();
            /** @var $oCmsDocument TdbCmsDocument */
            if ($oCmsDocument->Load($this->data)) {
                $currentFile = $oCmsDocument->GetDownloadLink();
            }
        }

        $html .= '<div class="alert alert-info" id="'.TGlobal::OutHTML($this->name).'currentFile">'.$currentFile."</div>
      <div class=\"cleardiv\">&nbsp;</div>\n";
        $html .= TCMSRender::DrawButton(TGlobal::Translate('chameleon_system_core.field_document.select'), 'javascript:'.$this->_GetOpenWindowJS(), URL_CMS.'/images/icons/page_attachment.gif', 'float-left');
        $html .= ' &nbsp; '.TCMSRender::DrawButton(TGlobal::Translate('chameleon_system_core.action.reset'), "javascript:_ResetDocument('".TGlobal::OutHTML($this->name)."', '".TGlobal::Translate('chameleon_system_core.field_document.nothing_selected')."','".TGlobal::OutHTML($this->oDefinition->sqlData['field_default_value'])."')", URL_CMS.'/images/icons/action_stop.gif', 'float-left');
        $html .= "<div class=\"cleardiv\">&nbsp;</div>\n";
        $html .= '<fieldset style="margin-top: 15px;">
        <legend>'.TGlobal::Translate('chameleon_system_core.field_document.upload')."</legend>\n";
        $html .= '
            <select name="documentTreeId_'.TGlobal::OutHTML($this->name).'" id="documentTreeId_'.TGlobal::OutHTML($this->name).'"  class="form-control form-control-sm float-left" style="max-width: 300px; width: auto;">
            <option value="" style="font-weight: bold;">'.TGlobal::Translate('chameleon_system_core.form.select_box_nothing_selected')."</option>\n";

        $oTreeSelect = new TCMSRenderDocumentTreeSelectBox();

        $html .= $oTreeSelect->GetTreeOptions();
        $html .= '
            </select>
          ';

        $html .= TCMSRender::DrawButton(TGlobal::Translate('chameleon_system_core.field_document.upload'), 'javascript:'.$this->_GetOpenUploadWindowJS(), URL_CMS.'/images/icons/add.png', 'float-left');

        $html .= "</fieldset>\n";

        $html .= "<div class=\"cleardiv\">&nbsp;</div>\n";
        $html .= '<div class="cmsdocumentfieldcontainer">';
        $html .= "</div>\n";

        return $html;
    }

    public function _GetOpenWindowJS()
    {
        $url = PATH_CMS_CONTROLLER.'?'.TTools::GetArrayAsURLForJavascript(array('pagedef' => 'CMSDocumentSelect', 'documentfieldname' => $this->name, 'tableid' => $this->oTableConf->id, 'id' => $this->recordId));
        $js = "saveCMSRegistryEntry('_currentFieldName','".TGlobal::OutHTML($this->name)."');CreateModalIFrameDialogCloseButton('".TGlobal::OutHTML($url)."',991,650);";

        return $js;
    }

    public function _GetOpenUploadWindowJS()
    {
        $js = "saveCMSRegistryEntry('_currentFieldName','".TGlobal::OutHTML($this->name)."');OpenUploadWindow(document.cmseditform.documentTreeId_".TGlobal::OutHTML($this->name).'.options[document.cmseditform.documentTreeId_'.TGlobal::OutHTML($this->name).'.selectedIndex].value);';

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
        $aIncludes = parent::GetCMSHtmlHeadIncludes();

        $url = PATH_CMS_CONTROLLER.'?pagedef=CMSUniversalUploader&mode=document&callback=_SetDocument&singleMode=1&treeNodeID=';
        $html = "<script type=\"text/javascript\">
      function OpenUploadWindow(documentTreeID) {
        if(documentTreeID != '') {
          CreateModalIFrameDialogCloseButton('".TGlobal::OutHTML($url)."' + documentTreeID,560,500);
        } else {
          toasterMessage('".TGlobal::Translate('chameleon_system_core.field_document.error_missing_target').".','ERROR');
        }
      }
      </script>";
        $aIncludes[] = $html;

        return $aIncludes;
    }
}
