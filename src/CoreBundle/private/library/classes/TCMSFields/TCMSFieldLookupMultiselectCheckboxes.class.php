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
use ChameleonSystem\CoreBundle\Util\UrlUtil;

/**
 * MLT field
 * it is possible to add a custom restriction using the "feldtyp konfiguration" from the field definition:
 *  restriction=expression
 *  - expression may contain references to the current record in the form [{fieldname}]
 *  : example: restriction=some_field_in_the_lookup_id='[{some_field_in_the_owning_record}]'
 *  the restriction will be added "as is" to the sql query.
 */
class TCMSFieldLookupMultiselectCheckboxes extends TCMSFieldLookupMultiselect
{
    public function GetHTML()
    {
        $sSQLTableName = $this->GetConnectedTableName();
        if ('_mlt' === substr($sSQLTableName, -4, 4)) {
            $sSQLTableName = substr($sSQLTableName, 0, -4);
        }

        $foreignTableName = $this->GetForeignTableName();
        $oTargetTableConf = new TCMSTableConf();
        $oTargetTableConf->LoadFromField('name', $foreignTableName);

        $sEscapedNameField = TGlobal::OutHTML($this->name);
        $html = "
      <div style=\"border-bottom: 1px solid #A9C4E7;\">
        <input type=\"hidden\" name=\"{$sEscapedNameField}[x]\" value=\"-\" id=\"{$sEscapedNameField}[]\" />

        <div style=\"float: right; width: 4px;\"><img src=\"".URL_CMS.'/images/boxTitleBgRight.gif" alt="" width="4" height="22" border="0" hspace="0" vspace="0" /></div>
        <div style="color: #151C55; float: left; width: 4px;"><img src="'.URL_CMS."/images/boxTitleBgLeft.gif\" alt=\"\" width=\"4\" height=\"22\" border=\"0\" hspace=\"0\" vspace=\"0\" /></div>
        <div class=\"listBoxTop\" style=\"cursor: default;\">
          <a href=\"javascript:markCheckboxes('".TGlobal::OutJS($this->name)."');\" class=\"checkBoxHeaderActionLink\" style=\"background: url(".URL_CMS.'/images/icons/accept.png) 0px 3px no-repeat;">'.TGlobal::Translate('chameleon_system_core.field_lookup_multi_select_checkboxes.select_deselect_all')."</a>
          <a href=\"javascript:invertCheckboxes('".TGlobal::OutJS($this->name)."')\" class=\"checkBoxHeaderActionLink\" style=\"margin-left: 10px; background: url(".URL_CMS.'/images/icons/arrow_switch.png) 0px 3px no-repeat;">'.TGlobal::Translate('chameleon_system_core.field_lookup_multi_select_checkboxes.invert_selection').'</a>
          ';

        if (true === $this->isRecordCreationAllowed($foreignTableName)) {
            $html .= "<a href=\"javascript:document.cmseditform.tableid.value='".TGlobal::OutJS($oTargetTableConf->sqlData['id'])."';ExecutePostCommand('Insert');\" class=\"checkBoxHeaderActionLink\" style=\"margin-left: 10px; background: url(".URL_CMS.'/images/icons/page_new.gif) 0px 3px no-repeat;">'.TGlobal::Translate('chameleon_system_core.field_lookup_multi_select.new').'</a>';
        }
        $urlUtil = $this->getUrlUtil();
        $bShowCustomsort = $this->oDefinition->GetFieldtypeConfigKey('bAllowCustomSortOrder');
        if (true == $bShowCustomsort) {
            $sSortUrl = $urlUtil->getArrayAsUrl(array(
                'pagedef' => 'CMSFieldMLTPosition',
                '_rmhist' => 'false',
                'module_fnc' => array('contentmodule' => 'GetSortElements'),
                'tableSQLName' => $sSQLTableName,
                'sRestriction' => $this->recordId,
                'sRestrictionField' => $this->sTableName.'_mlt',
            ), PATH_CMS_CONTROLLER.'?', '&');

            $html .= "<a href=\"javascript:parent.CreateModalIFrameDialogCloseButton('".$sSortUrl."',0,0,'".TGlobal::Translate('chameleon_system_core.field_lookup_multi_select.sort')."');\" class=\"checkBoxHeaderActionLink\" style=\"margin-left: 10px; background: url(".URL_CMS.'/images/icons/application_cascade.png) 0px 3px no-repeat;">'.TGlobal::Translate('chameleon_system_core.field_lookup_multi_select.sort').'</a>';
        }
        $html .= '<div class="cleardiv">&nbsp;</div>
        </div>
        <div class="cleardiv">&nbsp;</div>
      </div>
      <div style="padding: 5px; border: 1px solid #A9C4E7;">';

        $mltRecords = $this->getMltRecordData($oTargetTableConf->sqlData['list_group_field_column']);
        $activeGroup = '';
        $hasEditPermissionForForeignTable = $this->isRecordChangingAllowed($foreignTableName);
        foreach ($mltRecords as $mltRecord) {
            $recordId = $mltRecord['id'];
            $currentGroup = $mltRecord['group'];
            $connected = $mltRecord['connected'];
            $displayValue = $mltRecord['display_value'];
            $editable = $mltRecord['editable'];
            if ($currentGroup !== $activeGroup) {
                $activeGroup = $currentGroup;
                $html .= '<div style="clear:both;padding-top:10px;font-weight:bold;border-bottom:1px solid black" class="groupfield">'.TGlobal::OutHTML($currentGroup)."</div>\n";
            }

            $checked = '';
            if ($connected) {
                $checked = 'checked="checked"';
            }
            $disabled = '';
            if (false === $editable) {
                $disabled = 'disabled="disabled"';
            }

            $escapedRecordId = TGlobal::OutHTML($recordId);

            $escapedId = $sEscapedNameField.'_'.$escapedRecordId;

            $html .= '<div class="checkboxDIV">';
            $html .= '<div class="form-check form-check-inline">
                          <input class="form-check-input" type="checkbox" name="'.$sEscapedNameField.'['.$escapedRecordId.']" value="'.$escapedRecordId.'" id="'.$escapedId.'" '.$checked.' '.$disabled.'>
                          <label class="form-check-label" for="'.$escapedId.'">'.TGlobal::OutHTML($displayValue).'</label>
                      </div>';

            if ($hasEditPermissionForForeignTable) {
                $url = $urlUtil->getArrayAsUrl(
                    array(
                        'tableid' => $oTargetTableConf->sqlData['id'],
                        'pagedef' => 'tableeditor', 'id' => $recordId,
                    ),
                    PATH_CMS_CONTROLLER.'?'
                );
                $html .= "<div class=\"float-right\"><a href=\"$url\"><img src=\"".URL_CMS.'/images/icons/page_edit.gif" border="0"></a></div>';
            }

            $html .= '</div>';
        }

        $html .= '<div class="cleardiv">&nbsp;</div>
      </div>';

        return $html;
    }

    protected function isRecordCreationAllowed(string $foreignTableName): bool
    {
        $activeUser = TCMSUser::GetActiveUser();

        if (null === $activeUser) {
            return false;
        }

        return true === $activeUser->oAccessManager->HasNewPermission($foreignTableName);
    }

    protected function isRecordChangingAllowed(string $foreignTableName): bool
    {
        $activeUser = TCMSUser::GetActiveUser();

        if (null === $activeUser) {
            return false;
        }

        return true === $activeUser->oAccessManager->HasEditPermission($foreignTableName);
    }

    /**
     * Returns MLT data required for displaying the field. Subclasses may alter data such as if a record
     * should be editable.
     *
     * @param string $listGroupFieldColumn
     *
     * @return array
     */
    protected function getMltRecordData($listGroupFieldColumn)
    {
        $data = array();
        $mltTableName = $this->GetMLTTableName();
        $mltRecords = $this->FetchMLTRecords();

        while ($mltRecord = &$mltRecords->Next()) {
            $data[] = array(
                'id' => $mltRecord->id,
                'group' => '' === $listGroupFieldColumn ? '' : $mltRecord->sqlData[$listGroupFieldColumn],
                'connected' => $mltRecord->isConnected($this->sTableName, $this->oTableRow->sqlData['id'], $mltTableName),
                'display_value' => $mltRecord->GetDisplayValue(),
                'editable' => true,
            );
        }

        return $data;
    }

    protected function GetMLTRecordRestrictions()
    {
        $restrictions = $this->oDefinition->GetFieldtypeConfigKey('restriction');
        if (!empty($restrictions)) {
            $restrictions = ' '.$restrictions;
            // replace any fields...
            reset($this->oTableRow->sqlData);
            foreach ($this->oTableRow->sqlData as $key => $value) {
                if (!is_array($value)) {
                    $escapedValue = "'".MySqlLegacySupport::getInstance()->real_escape_string($value)."'";
                    $restrictions = str_replace('[{'.$key.'}]', $escapedValue, $restrictions);
                }
            }
            reset($this->oTableRow->sqlData);
        }

        $restrictions = trim($restrictions);
        if (!empty($restrictions)) {
            $restrictions = ' AND ('.$restrictions.')';
        }

        return $restrictions;
    }

    public function FetchMLTRecords()
    {
        // because the method could be called from frontend, we need to check if we are in cms mode... else use method from parent
        $oUser = &TCMSUser::GetActiveUser();
        if (TGlobal::IsCMSMode() && !is_null($oUser)) {
            $foreignTableName = $this->GetForeignTableName();
            $sFilterQuery = $this->GetMLTFilterQuery();

            /** @var $oMLTRecords TCMSRecordList */
            $sClassName = TCMSTableToClass::GetClassName(TCMSTableToClass::PREFIX_CLASS, $foreignTableName).'List';
            $oMLTRecords = call_user_func(array($sClassName, 'GetList'), $sFilterQuery, null, false);
            $oMLTRecords->SetLanguage($oUser->GetCurrentEditLanguageID());

            return $oMLTRecords;
        } else {
            return parent::FetchMLTRecords();
        }
    }

    /**
     * generates the filter query for FetchMLTRecords().
     *
     * @return string
     */
    protected function GetMLTFilterQuery()
    {
        $oUser = &TCMSUser::GetActiveUser();
        if (TGlobal::IsCMSMode() && !is_null($oUser)) {
            $foreignTableName = $this->GetForeignTableName();

            /** @var $oTableConf TCMSTableConf */
            $oTableConf = new TCMSTableConf();
            $oTableConf->LoadFromField('name', $foreignTableName);

            /** @var $oTableList TCMSListManagerFullGroupTable */
            $oTableList = &$oTableConf->GetListObject();

            $oTableList->sRestriction = null; // do not include the restriction - it is part of the parent table, not the mlt!

            $sFilterQuery = $oTableList->FilterQuery().$this->GetMLTRecordRestrictions();
            $sFilterQueryOrderInfo = $oTableList->GetSortInfoAsString();
            if (!empty($sFilterQueryOrderInfo)) {
                $sFilterQuery .= ' ORDER BY '.$sFilterQueryOrderInfo;
            }
        } else {
            $sFilterQuery = parent::GetMLTFilterQuery();
        }

        return $sFilterQuery;
    }

    public function GetSQL()
    {
        $bReadOnly = ('readonly' == $this->GetDisplayType());
        if (!$bReadOnly && ('readonly-if-filled' == $this->GetDisplayType())) {
            // empty? allow edit..
            $oTmpRecords = $this->FetchMLTRecords();
            if ($oTmpRecords && $oTmpRecords->Length() > 0) {
                $bReadOnly = true;
            }
        }
        if ($bReadOnly) {
            return false;
        } // prevent read only fields from saving

        if (is_array($this->oTableRow->sqlData) && array_key_exists('id', $this->oTableRow->sqlData) && !empty($this->oTableRow->sqlData['id'])) {
            $mltTableName = $this->GetMLTTableName();
            $recordId = $this->oTableRow->sqlData['id'];

            $oTableConf = new TCMSTableConf();
            $oTableConf->LoadFromField('name', $this->sTableName);

            if (is_array($this->data)) {
                $aConnectedIds = array();

                if (TGlobal::IsCMSMode()) {
                    $oAllForeignRecordsFiltered = $this->FetchMLTRecords();
                }

                $sAlreadyConnectedQuery = 'SELECT * FROM `'.MySqlLegacySupport::getInstance()->real_escape_string($mltTableName)."` WHERE `source_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($recordId)."'";
                $tRes = MySqlLegacySupport::getInstance()->query($sAlreadyConnectedQuery);
                while ($aRow = MySqlLegacySupport::getInstance()->fetch_assoc($tRes)) {
                    if (TGlobal::IsCMSMode()) {
                        if ($oAllForeignRecordsFiltered->FindItemsWithProperty('id', $aRow['target_id'])) {
                            $aConnectedIds[] = $aRow['target_id'];
                        }
                    } else {
                        $aConnectedIds[] = $aRow['target_id'];
                    }
                }

                $aNewConnections = array_diff($this->data, $aConnectedIds);
                $aDeletedConnections = array_diff($aConnectedIds, $this->data);

                $targetField = $this->name;

                /** @var $oTableEditor TCMSTableEditorManager */
                $oTableEditor = new TCMSTableEditorManager();
                $oTableEditor->Init($oTableConf->id, $recordId);

                foreach ($aDeletedConnections as $targetID) {
                    $oTableEditor->RemoveMLTConnection($targetField, $targetID);
                }

                foreach ($aNewConnections as $sKey => $targetID) {
                    if ('x' != $sKey) {
                        $oTableEditor->AddMLTConnection($targetField, $targetID);
                    }
                }
            }
        }

        return false; // prevent saving of sql
    }

    /**
     * returns true if field data is not empty
     * overwrite this method for mlt and property fields.
     *
     * @return bool
     */
    public function HasContent()
    {
        $bHasContent = false;
        if (is_array($this->data)) {
            if (array_key_exists('x', $this->data)) {
                if (count($this->data) > 1) {
                    $bHasContent = true;
                }
            } else {
                $bHasContent = true;
            }
        }

        return $bHasContent;
    }

    /**
     * @return UrlUtil
     */
    private function getUrlUtil()
    {
        return ServiceLocator::get('chameleon_system_core.util.url');
    }
}
