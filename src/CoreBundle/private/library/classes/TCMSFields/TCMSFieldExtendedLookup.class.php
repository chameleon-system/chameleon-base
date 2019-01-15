<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Component\Translation\TranslatorInterface;

/**
 * lookup.
/**/
class TCMSFieldExtendedLookup extends TCMSFieldLookup
{
    /**
     * generates HTML of the field for the backend (Table Editor).
     *
     * @return string
     */
    public function GetHTML()
    {
        $value = $this->_GetHTMLValue(); // the method returns the value ESCAPED

        $html = $this->_GetHiddenField();

        $html .= '<div class="input-group input-group-sm">';
        $html .= '<div id="'.TGlobal::OutHTML($this->name).'CurrentSelection" class="form-control form-control-sm">'.$value.'</div>';

        $goToRecordButtonHtml = $this->GetGoToRecordButton();

        if ('' !== $goToRecordButtonHtml) {
            $html .= '<div class="input-group-append">'.$goToRecordButtonHtml.'</div>';
        }

        $html .= '</div>';
        $html .= $this->GetExtendedListButtons();

        return $html;
    }

    /**
     * generates the HTML for the "go to record" button.
     *
     * @return string
     */
    protected function GetGoToRecordButton()
    {
        $sHTML = '';
        $sForeignTableName = $this->GetConnectedTableName();

        $oGlobal = TGlobal::instance();
        if ($this->bShowSwitchToRecord && $oGlobal->oUser->oAccessManager->HasNewPermission($sForeignTableName)) {
            $sHTML .= TCMSRender::DrawButton(TGlobal::Translate('chameleon_system_core.field_lookup.switch_to'), 'javascript:'.$this->GoToRecordJS().';', URL_CMS.'/images/icons/page_edit.gif');
        }

        return $sHTML;
    }

    /**
     * generates the javascript for the go to record button.
     *
     * @return string
     */
    public function GoToRecordJS()
    {
        $sTableName = $this->GetConnectedTableName();
        $oCmsTblConf = TdbCmsTblConf::GetNewInstance();
        $oCmsTblConf->LoadFromField('name', $sTableName);

        if ('cms_tpl_page' == $sTableName) { // for web pages, we need to force open the connected record in the main window because the template engine isn`t usable in a popup window
            $sJS = "GoToRecordByHiddenIdWithTarget('".$oCmsTblConf->id."','".TGlobal::OutHTML($this->name)."','top')";
        } else {
            $sJS = "GoToRecordByHiddenId('".$oCmsTblConf->id."','".TGlobal::OutHTML($this->name)."')";
        }

        return $sJS;
    }

    /**
     * generates HTML if the field has read-only mode.
     *
     * @return string
     */
    public function GetReadOnly()
    {
        $value = $this->_GetHTMLValue();

        $html = $this->_GetHiddenField();
        $html .= '<div class="input-group input-group-sm">';
        $html .= '<input type="text" class="form-control form-control-sm" value="'.TGlobal::OutHTML($value).'" readonly>';

        $goToRecordButtonHtml = $this->GetGoToRecordButton();

        if ('' !== $goToRecordButtonHtml) {
            $html .= '<div class="input-group-append">'.$goToRecordButtonHtml.'</div>';
        }

        $html .= '</div>';

        return $html;
    }

    /**
     * generates HTML for the buttons that open the layover with list of records.
     *
     * @return string
     */
    protected function GetExtendedListButtons()
    {
        $sTableName = $this->GetConnectedTableName();
        $oCmsTblConf = TdbCmsTblConf::GetNewInstance();
        $oCmsTblConf->LoadFromField('name', $sTableName);

        $sHTML = '<div class="btn-group mt-1">';
        $sHTML .= TCMSRender::DrawButton(TGlobal::Translate('chameleon_system_core.field_lookup.select_item'), 'javascript:'.$this->_GetOpenWindowJS($oCmsTblConf), URL_CMS.'/images/icons/box.gif', 'float-left');
        $sHTML .= TCMSRender::DrawButton(TGlobal::Translate('chameleon_system_core.action.reset'), "javascript:resetExtendedListField('".TGlobal::OutJS($this->name)."','".TGlobal::OutJS($this->oDefinition->sqlData['field_default_value'])."','".TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.field_lookup.nothing_selected'))."')", URL_CMS.'/images/icons/action_stop.gif', '');
        $sHTML .= '</div>';

        return $sHTML;
    }

    /**
     * generates the javascript for the extended list buttons.
     *
     * @param $oPopupTableConf
     *
     * @return string
     */
    protected function _GetOpenWindowJS(&$oPopupTableConf)
    {
        $aParams = array('pagedef' => 'extendedLookupList', 'id' => $oPopupTableConf->id, 'fieldName' => $this->name, 'sourceTblConfId' => $this->oDefinition->fieldCmsTblConfId);
        $sRestriction = $this->oDefinition->GetFieldtypeConfigKey('restriction');
        $aRestrictionParts = explode('=', $sRestriction);
        if (2 == count($aRestrictionParts) && property_exists($this, 'oTableRow') && is_object($this->oTableRow) && property_exists($this->oTableRow, 'sqlData') && is_array($this->oTableRow->sqlData) && array_key_exists($aRestrictionParts[0], $this->oTableRow->sqlData)) {
            $aParams['sRestriction'] = $aRestrictionParts[1];
            $aParams['sRestrictionField'] = $aRestrictionParts[0];
        }
        $sURL = PATH_CMS_CONTROLLER.'?'.TTools::GetArrayAsURLForJavascript($aParams);
        $translator = $this->getTranslationService();
        $sWindowTitle = $translator->trans('chameleon_system_core.field_lookup.select_item', array(), 'admin');

        $js = "CreateModalIFrameDialogCloseButton('".TGlobal::OutHTML($sURL)."',0,0,'".$sWindowTitle."');return false;";

        return $js;
    }

    /**
     * returns the value of the field for the backend (Table Editor).
     *
     * @return string
     */
    public function _GetHTMLValue()
    {
        $translator = $this->getTranslationService();
        if (empty($this->data)) {
            return $translator->trans('chameleon_system_core.field_lookup.nothing_selected', array(), 'admin');
        }

        $record = $this->getConnectedRecordObject();
        if (false !== $record) {
            return $record->GetDisplayValue();
        }

        return $translator->trans('chameleon_system_core.field_lookup.error_assigned_id_does_not_exists', array('%id%' => $this->data), 'admin');
    }

    /**
     * return the new charset latin1 so that we get more memory
     * size for a record.
     *
     * @return string
     */
    public function _GetSQLCharset()
    {
        return ' CHARACTER SET latin1 COLLATE latin1_general_ci';
    }

    /**
     * @return TranslatorInterface
     */
    private function getTranslationService()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('translator');
    }
}
