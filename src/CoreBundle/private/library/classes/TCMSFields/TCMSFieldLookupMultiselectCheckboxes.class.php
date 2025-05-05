<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CmsBackendBundle\BackendSession\BackendSessionInterface;
use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;
use ChameleonSystem\CoreBundle\Util\UrlUtil;
use ChameleonSystem\SecurityBundle\Service\SecurityHelperAccess;
use ChameleonSystem\SecurityBundle\Voter\CmsPermissionAttributeConstants;
use ChameleonSystem\SecurityBundle\Voter\CmsUserRoleConstants;

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
        $html = '<input type="hidden" name="'.$sEscapedNameField.'[x]" value="-" id="'.$sEscapedNameField.'[]" />
      <div class="card">
        <div class="card-header p-1">
          <a href="javascript:markCheckboxes(\''.TGlobal::OutJS($this->name).'\');" class="checkBoxHeaderActionLink"><i class="fas fa-check pr-2"></i>'.ServiceLocator::get('translator')->trans('chameleon_system_core.field_lookup_multi_select_checkboxes.select_deselect_all').'</a>
          <a href="javascript:invertCheckboxes(\''.TGlobal::OutJS($this->name).'\');" class="checkBoxHeaderActionLink ml-2"><i class="fas fa-random pr-2"></i>'.ServiceLocator::get('translator')->trans('chameleon_system_core.field_lookup_multi_select_checkboxes.invert_selection').'</a>';

        if (true === $this->isRecordCreationAllowed($foreignTableName)) {
            $html .= '<a href="javascript:document.cmseditform.tableid.value=\''.TGlobal::OutJS($oTargetTableConf->sqlData['id']).'\';ExecutePostCommand(\'Insert\');" class="checkBoxHeaderActionLink ml-2"><i class="fas fa-plus pr-2"></i>'.ServiceLocator::get('translator')->trans('chameleon_system_core.field_lookup_multi_select.new').'</a>';
        }
        $urlUtil = $this->getUrlUtil();
        $bShowCustomsort = $this->oDefinition->GetFieldtypeConfigKey('bAllowCustomSortOrder');
        if (true == $bShowCustomsort) {
            $sSortUrl = $urlUtil->getArrayAsUrl([
                'pagedef' => 'CMSFieldMLTPosition',
                '_rmhist' => 'false',
                'module_fnc' => ['contentmodule' => 'GetSortElements'],
                'tableSQLName' => $sSQLTableName,
                'sRestriction' => $this->recordId,
                'sRestrictionField' => $this->sTableName.'_mlt',
            ], PATH_CMS_CONTROLLER.'?', '&');

            $html .= '<a href="javascript:parent.CreateModalIFrameDialogCloseButton(\''.$sSortUrl.'\',0,0,\''.ServiceLocator::get('translator')->trans('chameleon_system_core.field_lookup_multi_select.sort').'\');" class="checkBoxHeaderActionLink ml-2"><i class="fas fa-sort-amount-down pr-2"></i>'.ServiceLocator::get('translator')->trans('chameleon_system_core.field_lookup_multi_select.sort').'</a>';
        }
        $html .= '
        </div>
        <div class="card-body p-1">
        <div class="checkbox-matrix">';

        $mltRecords = $this->getMltRecordData($oTargetTableConf->sqlData['list_group_field_column']);
        $activeGroup = '';
        $hasEditPermissionForForeignTable = $this->isRecordChangingAllowed($foreignTableName);

        $mltRecords = $this->sortRecordsAlphabetically($mltRecords);

        $inputFilter = $this->getInputFilterUtil();

        foreach ($mltRecords as $mltRecord) {
            $recordId = $mltRecord['id'];
            $currentGroup = $mltRecord['group'];
            $connected = $mltRecord['connected'];
            $displayValue = $mltRecord['display_value'];

            $editable = $mltRecord['editable'];
            if ($currentGroup !== $activeGroup) {
                $activeGroup = $currentGroup;
                $html .= '<div class="checkboxGroupTitle">'.TGlobal::OutHTML($currentGroup)."</div>\n";
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
            $html .= '<div class="form-check form-switch form-switch-lg">
                          <input class="form-check-input" type="checkbox" name="'.$sEscapedNameField.'['.$escapedRecordId.']" value="'.$escapedRecordId.'" id="'.$escapedId.'" '.$checked.' '.$disabled.'>
                          '.TGlobal::OutHTML($displayValue).'
                          <label class="form-check-label" for="'.$escapedId.'">
                      </div>';

            if (true === $hasEditPermissionForForeignTable) {
                $url = $urlUtil->getArrayAsUrl(
                    [
                        'tableid' => $oTargetTableConf->sqlData['id'],
                        'pagedef' => $inputFilter->getFilteredGetInput('pagedef', 'tableeditor'),
                        'id' => $recordId,
                    ],
                    PATH_CMS_CONTROLLER.'?'
                );
                $html .= '<div class="entry-edit"><a href="'.$url.'"><i class="fas fa-edit"></i></a></div>';
            }

            $html .= '</div>';
        }

        $html .= '</div></div></div>';

        return $html;
    }

    private function sortRecordsAlphabetically(array $mltRecords): array
    {
        \usort($mltRecords, static function (array $entry1, array $entry2) {
            if ($entry1['group'] !== $entry2['group']) {
                return \strcasecmp($entry1['group'], $entry2['group']);
            }

            return \strcasecmp($entry1['display_value'], $entry2['display_value']);
        });

        return $mltRecords;
    }

    protected function isRecordCreationAllowed(string $foreignTableName): bool
    {
        /** @var SecurityHelperAccess $securityHelper */
        $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);

        return $securityHelper->isGranted(CmsPermissionAttributeConstants::TABLE_EDITOR_NEW, $foreignTableName);
    }

    protected function isRecordChangingAllowed(string $foreignTableName): bool
    {
        /** @var SecurityHelperAccess $securityHelper */
        $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);

        return $securityHelper->isGranted(CmsPermissionAttributeConstants::TABLE_EDITOR_EDIT, $foreignTableName);
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
        $data = [];
        $mltTableName = $this->GetMLTTableName();
        $mltRecords = $this->FetchMLTRecords();

        while ($mltRecord = $mltRecords->Next()) {
            $data[] = [
                'id' => $mltRecord->id,
                'group' => '' === $listGroupFieldColumn ? '' : $mltRecord->sqlData[$listGroupFieldColumn],
                'connected' => $mltRecord->isConnected($this->sTableName, $this->oTableRow->sqlData['id'], $mltTableName),
                'display_value' => $mltRecord->GetName(), // use name here (and not GetDisplayValue) as this will be used as simple label
                'editable' => true,
            ];
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
        if (false === TGlobal::IsCMSMode()) {
            return parent::FetchMLTRecords();
        }

        /** @var SecurityHelperAccess $securityHelper */
        $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);

        if (false === $securityHelper->isGranted(CmsUserRoleConstants::CMS_USER)) {
            return parent::FetchMLTRecords();
        }
        /** @var BackendSessionInterface $backendSession */
        $backendSession = ServiceLocator::get('chameleon_system_cms_backend.backend_session');

        $foreignTableName = $this->GetForeignTableName();
        $sFilterQuery = $this->GetMLTFilterQuery();

        /** @var $oMLTRecords TCMSRecordList */
        $sClassName = TCMSTableToClass::GetClassName(TCMSTableToClass::PREFIX_CLASS, $foreignTableName).'List';
        $oMLTRecords = call_user_func([$sClassName, 'GetList'], $sFilterQuery, null, false);
        $editLanguageId = $backendSession->getCurrentEditLanguageId();
        $oMLTRecords->SetLanguage($editLanguageId);

        return $oMLTRecords;
    }

    /**
     * generates the filter query for FetchMLTRecords().
     *
     * @return string
     */
    protected function GetMLTFilterQuery()
    {
        // because the method could be called from frontend, we need to check if we are in cms mode... else use method from parent
        if (false === TGlobal::IsCMSMode()) {
            return parent::GetMLTFilterQuery();
        }

        /** @var SecurityHelperAccess $securityHelper */
        $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);

        if (false === $securityHelper->isGranted(CmsUserRoleConstants::CMS_USER)) {
            return parent::GetMLTFilterQuery();
        }

        $foreignTableName = $this->GetForeignTableName();

        /** @var $oTableConf TCMSTableConf */
        $oTableConf = new TCMSTableConf();
        $oTableConf->LoadFromField('name', $foreignTableName);

        /** @var $oTableList TCMSListManagerFullGroupTable */
        $oTableList = $oTableConf->GetListObject(ignoreTableEditorRestrictions: true);

        $oTableList->sRestriction = null; // do not include the restriction - it is part of the parent table, not the mlt!

        $sFilterQuery = $oTableList->FilterQuery().$this->GetMLTRecordRestrictions();

        $sFilterQueryOrderInfo = $oTableList->GetSortInfoAsString();
        if (!empty($sFilterQueryOrderInfo)) {
            $sFilterQuery .= ' ORDER BY '.$sFilterQueryOrderInfo;
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
            $recordId = $this->oTableRow->sqlData['id'];

            $oTableConf = new TCMSTableConf();
            $oTableConf->LoadFromField('name', $this->sTableName);

            if (is_array($this->data)) {
                $aConnectedIds = $this->getMltValues();

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

    private function getUrlUtil(): UrlUtil
    {
        return ServiceLocator::get('chameleon_system_core.util.url');
    }

    private function getInputFilterUtil(): InputFilterUtilInterface
    {
        return ServiceLocator::get('chameleon_system_core.util.input_filter');
    }
}
