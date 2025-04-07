<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\Interfaces\FlashMessageServiceInterface;
use ChameleonSystem\CoreBundle\ServiceLocator;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * lookup.
 **/
class TCMSFieldExtendedLookup extends TCMSFieldLookup
{
    /**
     * Generates HTML of the field for the backend (Table Editor).
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
     * Generates the HTML for the "go to record" button.
     *
     * @return string
     */
    protected function GetGoToRecordButton()
    {
        $sHTML = '';
        $tableName = $this->GetConnectedTableName();

        $target = null;
        if ('cms_tpl_page' === $tableName) {
            // for web pages, we need to force open the connected record in the main window because the template engine isn`t usable in a popup window
            $target = 'top';
        }

        $oGlobal = TGlobal::instance();
        if ($this->bShowSwitchToRecord && $oGlobal->oUser->oAccessManager->HasNewPermission($tableName)) {
            $sHTML .= TCMSRender::DrawButton(
                TGlobal::Translate('chameleon_system_core.field_lookup.switch_to'),
                $this->getSelectedEntryLink($this->data),
                'far fa-edit',
                null,
                null,
                null,
                null,
                $target
            );
        }

        return $sHTML;
    }

    /**
     * @deprecated not used anymore
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

        $sHTML = '<div class="row button-element mt-1">';
        $sHTML .= '<div class="button-item col-auto">';
        $sHTML .= TCMSRender::DrawButton(TGlobal::Translate('chameleon_system_core.field_lookup.select_item'), 'javascript:'.$this->_GetOpenWindowJS($oCmsTblConf), 'far fa-check-circle', 'float-left');
        $sHTML .= '</div>';
        $sHTML .= '<div class="button-item col-auto">';
        $sHTML .= TCMSRender::DrawButton(TGlobal::Translate('chameleon_system_core.action.reset'), "javascript:resetExtendedListField('".TGlobal::OutJS($this->name)."','".TGlobal::OutJS($this->oDefinition->sqlData['field_default_value'])."','".TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.field_lookup.nothing_selected'))."')", 'far fa-times-circle', '');
        $sHTML .= '</div>';
        $sHTML .= '</div>';

        return $sHTML;
    }

    /**
     * generates the javascript for the extended list buttons.
     *
     * @param TCMSRecord $oPopupTableConf
     *
     * @return string
     */
    protected function _GetOpenWindowJS(&$oPopupTableConf)
    {
        $aParams = ['pagedef' => 'extendedLookupList', 'id' => $oPopupTableConf->id, 'fieldName' => $this->name, 'sourceTblConfId' => $this->oDefinition->fieldCmsTblConfId];
        $restriction = $this->getTargetListRestriction();

        if (0 !== \count($restriction)) {
            $aParams = array_merge($aParams, $restriction);
        }

        $targetListClass = $this->oDefinition->GetFieldtypeConfigKey('targetListClass');
        if (null !== $targetListClass) {
            if (true === \class_exists($targetListClass)) {
                $aParams += ['targetListClass' => $targetListClass];
            } else {
                $this->getFlashMessageService()->addBackendToasterMessage('chameleon_system_core.field_lookup.invalid_target_list_class', 'ERROR', [
                    '%field%' => $this->oDefinition->GetName(),
                    '%targetListClass%' => addcslashes($targetListClass, '\\'),
                ]);
            }
        }

        $sURL = PATH_CMS_CONTROLLER.'?'.TTools::GetArrayAsURLForJavascript($aParams);
        $translator = $this->getTranslationService();
        $sWindowTitle = $translator->trans('chameleon_system_core.field_lookup.select_item', [], 'admin');

        $js = "CreateModalIFrameDialogCloseButton('".TGlobal::OutHTML($sURL)."',0,0,'".$sWindowTitle."');return false;";

        return $js;
    }

    private function getTargetListRestriction(): array
    {
        $restrictionExpression = $this->oDefinition->GetFieldtypeConfigKey('restriction');
        if (null === $restrictionExpression) {
            return [];
        }
        if (false === \strpos($restrictionExpression, '=')) {
            return [];
        }
        $equalPosition = \mb_strpos($restrictionExpression, '=');
        $restrictionField = \trim(\mb_substr($restrictionExpression, 0, $equalPosition));
        $restrictionValue = \mb_substr($restrictionExpression, $equalPosition + 1);

        if (true === $this->isFieldToken($restrictionValue)) {
            $restrictionValueReplaced = $this->getFieldValueFromFieldToken($restrictionValue);
            if (null === $restrictionValueReplaced) {
                $this->getFlashMessageService()->addBackendToasterMessage('chameleon_system_core.field_lookup.invalid_restriction', 'ERROR', [
                    '%field%' => $this->oDefinition->GetName(),
                    '%restriction%' => $restrictionExpression,
                ]);
                $restrictionValueReplaced = \sprintf('field token %s not found in record.', $restrictionValue);
            }
            $restrictionValue = $restrictionValueReplaced;
        }

        if ('' === $restrictionField || '' === $restrictionValue) {
            return [];
        }

        return [
            'sRestrictionField' => $restrictionField,
            'sRestriction' => $restrictionValue,
        ];
    }

    private function getFlashMessageService(): FlashMessageServiceInterface
    {
        return ServiceLocator::get('chameleon_system_core.flash_messages');
    }

    private function isFieldToken(string $string): bool
    {
        $string = \trim($string);

        return 0 === \mb_strpos($string, '[{') && '}]' === \mb_substr($string, -2);
    }

    /**
     * Returns field value in current record if token matches a known field. Otherwise, null will be returned.
     *
     * @param string $fieldToken - token in the form [{fieldName}]
     */
    private function getFieldValueFromFieldToken(string $fieldToken): ?string
    {
        $fieldToken = \trim($fieldToken);
        $fieldName = \trim(\mb_substr($fieldToken, 2, -2));

        return $this->oTableRow->sqlData[$fieldName] ?? null;
    }

    /**
     * Returns the value of the field for the backend (Table Editor).
     *
     * @return string
     */
    public function _GetHTMLValue()
    {
        $translator = $this->getTranslationService();
        if (empty($this->data)) {
            return $translator->trans('chameleon_system_core.field_lookup.nothing_selected', [], 'admin');
        }

        $record = $this->getConnectedRecordObject();
        if (false !== $record) {
            return $record->getDisplayTitle(-1);
        }

        return $translator->trans('chameleon_system_core.field_lookup.error_assigned_id_does_not_exists', ['%id%' => $this->data], 'admin');
    }

    /**
     * Returns the new charset latin1 so that we get more memory
     * size for a record.
     *
     * @return string
     */
    public function _GetSQLCharset()
    {
        return ' CHARACTER SET latin1 COLLATE latin1_general_ci';
    }

    private function getTranslationService(): TranslatorInterface
    {
        return ServiceLocator::get('translator');
    }
}
