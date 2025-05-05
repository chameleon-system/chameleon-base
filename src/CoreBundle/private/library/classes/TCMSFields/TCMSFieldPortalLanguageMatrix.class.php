<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\AutoclassesBundle\TableConfExport\DoctrineNotTransformableMarkerInterface;
use ChameleonSystem\DatabaseMigration\DataModel\LogChangeDataModel;
use ChameleonSystem\DatabaseMigration\Query\MigrationQueryData;

/**
 * Renders a list of languages per portal to allow language specific selections.
 * you can call the filtered selection using the function: TdbYourTable::GetListForPortalLanguage();.
 */
class TCMSFieldPortalLanguageMatrix extends TCMSField implements DoctrineNotTransformableMarkerInterface
{
    // Only used in a single project. We'll need to handle it there.

    /**
     * indicates if GetHTML should render the form in read only mode.
     *
     * @var bool
     */
    protected $bReadOnlyMode = false;

    /**
     * SQL name of matrix table.
     *
     * @var string
     */
    protected $sMatrixTableName = 'cms_portal_language_matrix';

    public function GetHTML()
    {
        $sEscapedNameField = TGlobal::OutHTML($this->name);
        $html = '<input type="hidden" name="'.$sEscapedNameField.'[x]" value="-" id="'.$sEscapedNameField.'[]" />
      <div class="card">
        <div class="card-header p-1">
          ';

        if (!$this->bReadOnlyMode) {
            $html .= '<a href="javascript:markCheckboxes(\''.TGlobal::OutJS($this->name).'\');" class="checkBoxHeaderActionLink"><i class="fas fa-check pr-2"></i>'.ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.field_lookup_multi_select_checkboxes.select_deselect_all').'</a>
          <a href="javascript:invertCheckboxes(\''.TGlobal::OutJS($this->name).'\');" class="checkBoxHeaderActionLink ml-2"><i class="fas fa-random pr-2"></i>'.ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.field_lookup_multi_select_checkboxes.invert_selection').'</a>
          ';
        }

        $html .= '
        </div>  
        <div class="card-body p1">  
          ';

        $oTableConfig = $this->oTableRow->GetTableConf();
        $sRecordID = $this->oTableRow->sqlData['id'];
        $oPortalList = TdbCmsPortalList::GetList();

        while ($oPortal = $oPortalList->Next()) {
            $html .= '<div class="checkboxGroupTitle">'.TGlobal::OutHTML($oPortal->fieldName)."</div>\n";

            // load base language
            $oBaseLanguage = $oPortal->GetFieldCmsLanguage();

            $checked = '';

            $sQuery = 'SELECT * FROM `'.MySqlLegacySupport::getInstance()->real_escape_string($this->sMatrixTableName).'` WHERE
                `'.MySqlLegacySupport::getInstance()->real_escape_string($this->sMatrixTableName)."`.`record_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($sRecordID)."'
                AND `".MySqlLegacySupport::getInstance()->real_escape_string($this->sMatrixTableName)."`.`cms_tbl_conf_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($oTableConfig->id)."'
                AND `".MySqlLegacySupport::getInstance()->real_escape_string($this->sMatrixTableName)."`.`cms_portal_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($oPortal->id)."'
                AND `".MySqlLegacySupport::getInstance()->real_escape_string($this->sMatrixTableName)."`.`cms_language_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($oBaseLanguage->id)."'
                ";

            $result = MySqlLegacySupport::getInstance()->query($sQuery);

            if (MySqlLegacySupport::getInstance()->num_rows($result) > 0) {
                $checked = ' checked="checked"';
            }

            $BackgroundColor = ' style="background-color: #FFFFFF;"';

            $html .= '<div class="checkboxDIV"'.$BackgroundColor.'>';
            $html .= '<label style="float: left;">';

            if (!$this->bReadOnlyMode) {
                $html .= "<input type=\"checkbox\" class=\"checkbox\" name=\"{$sEscapedNameField}[".TGlobal::OutHTML(md5($oPortal->id.$oBaseLanguage->id)).']" value="'.TGlobal::OutHTML($oPortal->id.'|'.$oBaseLanguage->id)."\" id=\"{$sEscapedNameField}[]\"{$checked} />";
            }
            $html .= TGlobal::OutHTML($oBaseLanguage->GetDisplayValue());
            $html .= '</label>';
            $html .= "\n<div class=\"cleardiv\">&nbsp;</div>\n";
            $html .= '</div>';

            // load available languages
            $oLanguageList = $oPortal->GetFieldCmsLanguageList();
            $count = 0;
            while ($oLanguage = $oLanguageList->Next()) {
                ++$count;

                $checked = '';

                $sQuery = 'SELECT * FROM `'.MySqlLegacySupport::getInstance()->real_escape_string($this->sMatrixTableName).'` WHERE
                `'.MySqlLegacySupport::getInstance()->real_escape_string($this->sMatrixTableName)."`.`record_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($sRecordID)."'
                AND `".MySqlLegacySupport::getInstance()->real_escape_string($this->sMatrixTableName)."`.`cms_tbl_conf_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($oTableConfig->id)."'
                AND `".MySqlLegacySupport::getInstance()->real_escape_string($this->sMatrixTableName)."`.`cms_portal_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($oPortal->id)."'
                AND `".MySqlLegacySupport::getInstance()->real_escape_string($this->sMatrixTableName)."`.`cms_language_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($oLanguage->id)."'

                ";
                $result = MySqlLegacySupport::getInstance()->query($sQuery);

                if (MySqlLegacySupport::getInstance()->num_rows($result) > 0) {
                    $checked = ' checked="checked"';
                }

                $BackgroundColor = ' style="background-color: #FFFFFF;"';
                if ($count % 2) {
                    $BackgroundColor = ' style="background-color: #F2F8FC;"';
                }

                $html .= '<div class="checkboxDIV"'.$BackgroundColor.'>';
                $html .= '<label style="float: left;">';

                if (!$this->bReadOnlyMode) {
                    $html .= "<input type=\"checkbox\" class=\"checkbox\" name=\"{$sEscapedNameField}[".TGlobal::OutHTML(md5($oPortal->id.$oLanguage->id)).']" value="'.TGlobal::OutHTML($oPortal->id.'|'.$oLanguage->id)."\" id=\"{$sEscapedNameField}[]\"{$checked} />";
                }
                $html .= TGlobal::OutHTML($oLanguage->GetDisplayValue());
                $html .= '</label>';
                $html .= "\n<div class=\"cleardiv\">&nbsp;</div>\n";
                $html .= '</div>';
            }
        }

        $html .= '<div class="cleardiv">&nbsp;</div>
            </div>
        </div>';

        return $html;
    }

    /**
     * creates any related tables (like mlt tables). the function will be called
     * from the outside whenever there is a change of type TO this type of field.
     *
     * @param bool $returnDDL
     *
     * @return string
     */
    public function CreateRelatedTables($returnDDL = false)
    {
        $sReturnVal = '';
        if (!TGlobal::TableExists($this->sMatrixTableName)) {
            $query = 'CREATE TABLE `'.MySqlLegacySupport::getInstance()->real_escape_string($this->sMatrixTableName).'` (
                  `record_id` CHAR( 36 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL ,
                  `cms_tbl_conf_id` char(36) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
                  `cms_portal_id` char(36) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
                  `cms_language_id` char(36) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
                  PRIMARY KEY (`record_id`, `cms_tbl_conf_id`, `cms_portal_id` , `cms_language_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8';

            if (!$returnDDL) {
                MySqlLegacySupport::getInstance()->query($query);
                $aQuery = [new LogChangeDataModel($query)];

                TCMSLogChange::WriteTransaction($aQuery);
            } else {
                $sReturnVal .= $query.";\n";
            }
        }

        return $sReturnVal;
    }

    /**
     * removes any related tables (like mlt tables). the function will be called
     * from the outside whenever there is a change of type FROM this type of field.
     */
    public function DeleteRelatedTables()
    {
        // drop table is disabled because we need to check if the field type is used in another table
        $oTableConfig = $this->oTableRow->GetTableConf();
        $sQuery = "DELETE FROM `cms_portal_language_matrix` WHERE `cms_portal_language_matrix`.`cms_tbl_conf_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($oTableConfig->id)."'";
        MySqlLegacySupport::getInstance()->query($sQuery);

        $editLanguageIsoCode = $this->getBackendSession()->getCurrentEditLanguageIso6391();
        $migrationQueryData = new MigrationQueryData('cms_portal_language_matrix', $editLanguageIsoCode);
        $migrationQueryData
            ->setWhereEquals([
              'cms_tbl_conf_id' => $oTableConfig->id,
            ])
        ;
        $aQuery = [new LogChangeDataModel($migrationQueryData, LogChangeDataModel::TYPE_DELETE)];
        TCMSLogChange::WriteTransaction($aQuery);
    }

    public function GetSQL()
    {
        $oTableConfig = $this->oTableRow->GetTableConf();

        $bReadOnly = ('readonly' == $this->GetDisplayType());
        if (!$bReadOnly && ('readonly-if-filled' == $this->GetDisplayType())) {
            // empty? allow edit..

            $sQuery = 'SELECT * FROM `'.MySqlLegacySupport::getInstance()->real_escape_string($this->sMatrixTableName).'` WHERE
                `'.MySqlLegacySupport::getInstance()->real_escape_string($this->sMatrixTableName)."`.`record_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->recordId)."'
                AND `".MySqlLegacySupport::getInstance()->real_escape_string($this->sMatrixTableName)."`.`cms_tbl_conf_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($oTableConfig->id)."'
                ";

            $result = MySqlLegacySupport::getInstance()->query($sQuery);

            if (MySqlLegacySupport::getInstance()->num_rows($result) > 0) {
                $bReadOnly = true;
            }
        }
        if ($bReadOnly) {
            return false;
        } // prevent read only fields from saving

        if (is_array($this->oTableRow->sqlData) && array_key_exists('id', $this->oTableRow->sqlData) && !empty($this->oTableRow->sqlData['id'])) {
            $oTableConfig = $this->oTableRow->GetTableConf();
            $sRecordID = $this->oTableRow->sqlData['id'];

            if (is_array($this->data)) {
                $aConnectedIds = [];

                $sAlreadyConnectedQuery = 'SELECT * FROM `'.MySqlLegacySupport::getInstance()->real_escape_string($this->sMatrixTableName).'`
                WHERE `'.MySqlLegacySupport::getInstance()->real_escape_string($this->sMatrixTableName)."`.`record_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($sRecordID)."'
                AND `".MySqlLegacySupport::getInstance()->real_escape_string($this->sMatrixTableName)."`.`cms_tbl_conf_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($oTableConfig->id)."'";
                $tRes = MySqlLegacySupport::getInstance()->query($sAlreadyConnectedQuery);
                while ($aRow = MySqlLegacySupport::getInstance()->fetch_assoc($tRes)) {
                    $aConnectedIds[] = $aRow['cms_portal_id'].'|'.$aRow['cms_language_id'];
                }

                $aNewConnections = array_diff($this->data, $aConnectedIds);
                $aDeletedConnections = array_diff($aConnectedIds, $this->data);

                foreach ($aDeletedConnections as $sValue) {
                    $aValueParts = explode('|', $sValue);
                    $sPortalID = $aValueParts[0];
                    $sLanguageID = $aValueParts[1];

                    $sQuery = 'DELETE FROM `'.MySqlLegacySupport::getInstance()->real_escape_string($this->sMatrixTableName).'` WHERE
                    `'.MySqlLegacySupport::getInstance()->real_escape_string($this->sMatrixTableName)."`.`record_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($sRecordID)."'
                    AND `".MySqlLegacySupport::getInstance()->real_escape_string($this->sMatrixTableName)."`.`cms_tbl_conf_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($oTableConfig->id)."'
                    AND `".MySqlLegacySupport::getInstance()->real_escape_string($this->sMatrixTableName)."`.`cms_portal_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($sPortalID)."'
                    AND `".MySqlLegacySupport::getInstance()->real_escape_string($this->sMatrixTableName)."`.`cms_language_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($sLanguageID)."'
                    ";

                    MySqlLegacySupport::getInstance()->query($sQuery);
                }

                foreach ($aNewConnections as $sKey => $sValue) {
                    if ('x' != $sKey) {
                        $aValueParts = explode('|', $sValue);
                        $sPortalID = $aValueParts[0];
                        $sLanguageID = $aValueParts[1];

                        $sQuery = 'SELECT * FROM `'.MySqlLegacySupport::getInstance()->real_escape_string($this->sMatrixTableName).'` WHERE
                        `'.MySqlLegacySupport::getInstance()->real_escape_string($this->sMatrixTableName)."`.`record_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($sRecordID)."'
                        AND `".MySqlLegacySupport::getInstance()->real_escape_string($this->sMatrixTableName)."`.`cms_tbl_conf_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($oTableConfig->id)."'
                        AND `".MySqlLegacySupport::getInstance()->real_escape_string($this->sMatrixTableName)."`.`cms_portal_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($sPortalID)."'
                        AND `".MySqlLegacySupport::getInstance()->real_escape_string($this->sMatrixTableName)."`.`cms_language_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($sLanguageID)."'
                        ";

                        $result = MySqlLegacySupport::getInstance()->query($sQuery);
                        if (0 == MySqlLegacySupport::getInstance()->num_rows($result)) {
                            $sQuery = 'INSERT INTO `'.MySqlLegacySupport::getInstance()->real_escape_string($this->sMatrixTableName)."` SET
                            `record_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($sRecordID)."',
                            `cms_tbl_conf_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($oTableConfig->id)."',
                            `cms_portal_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($sPortalID)."',
                            `cms_language_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($sLanguageID)."'
                            ";

                            MySqlLegacySupport::getInstance()->query($sQuery);
                        }
                    }
                }
            }
        }

        return false; // prevent saving of sql
    }

    public function GetReadOnly()
    {
        $this->bReadOnlyMode = true;
        $html = $this->GetHTML();

        return $html;
    }

    public function _GetHiddenField()
    {
        return '';
    }

    /**
     * render any methods for the auto list class for this field.
     *
     * @return string
     */
    public function RenderFieldListMethodsString()
    {
        $sCode = parent::RenderFieldListMethodsString();

        $sTableForMethodParameterDocumentation = 'Portal Language';

        $aMethodData = $this->GetFieldMethodBaseDataArray();
        $sMethodName = 'GetListForPortalLanguage';

        $aMethodData['sMethodName'] = $sMethodName;
        $aMethodData['sReturnType'] = TCMSTableToClass::GetClassName(TCMSTableToClass::PREFIX_CLASS, $this->sTableName).'List';

        $aMethodData['sClassName'] = $aMethodData['sReturnType'];
        $aMethodData['sClassSubType'] = 'CMSDataObjects';
        $aMethodData['sVisibility'] = 'static public';
        $aMethodData['sTableDatabaseName'] = $this->sTableName;
        $aMethodData['sTableConfId'] = TTools::GetCMSTableId($this->sTableName);
        $aMethodData['aFieldData']['sFieldFullName'] = 'Return all records belonging to the '.$sTableForMethodParameterDocumentation;

        $oViewParser = new TViewParser();
        $oViewParser->bShowTemplatePathAsHTMLHint = false;
        $oViewParser->AddVarArray($aMethodData);

        $sMethodCode = $oViewParser->RenderObjectView('listmethodcode', 'TCMSFields/TCMSFieldPortalLanguageMatrix');
        $oViewParser->AddVar('sMethodCode', $sMethodCode);
        $sCode .= $oViewParser->RenderObjectView('method', 'TCMSFields/TCMSField');

        return $sCode;
    }

    /**
     * changes an existing field definition (alter table).
     *
     * @param string $sOldName
     * @param string $sNewName
     * @param array|null $postData
     */
    public function ChangeFieldDefinition($sOldName, $sNewName, $postData = null)
    {
    }

    /**
     * update default value of the field.
     *
     * @param string $sFieldDefaultValue
     * @param string $sFieldName
     * @param bool $bUpdateExistingRecords
     */
    protected function UpdateFieldDefaultValue($sFieldDefaultValue, $sFieldName, $bUpdateExistingRecords = false)
    {
    }

    /**
     * create a new field definition (alter table).
     *
     * @param bool $returnDDL
     * @param TCMSField $oField
     *
     * @return string
     */
    public function CreateFieldDefinition($returnDDL = false, $oField = null)
    {
        return '';
    }

    /**
     * drops the field index.
     */
    public function RemoveFieldIndex()
    {
    }

    /**
     * sets field index if the field type is indexable.
     *
     * @param bool $returnDDL - if tre the SQL alter statement will be returned
     *
     * @return string
     */
    public function CreateFieldIndex($returnDDL = false)
    {
        if ($returnDDL) {
            return '';
        }
    }

    /**
     * generate the field definition part of the sql statement
     * we assume that $oFieldDefinition holds the correct default value.
     *
     * @param array $fieldDefinition
     *
     * @return string
     */
    public function _GetSQLDefinition($fieldDefinition = null)
    {
        return '';
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

        $oTableConfig = $this->oTableRow->GetTableConf();
        $sRecordID = $this->oTableRow->sqlData['id'];

        $sAlreadyConnectedQuery = 'SELECT * FROM `'.MySqlLegacySupport::getInstance()->real_escape_string($this->sMatrixTableName).'`
                WHERE `'.MySqlLegacySupport::getInstance()->real_escape_string($this->sMatrixTableName)."`.`record_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($sRecordID)."'
                AND `".MySqlLegacySupport::getInstance()->real_escape_string($this->sMatrixTableName)."`.`cms_tbl_conf_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($oTableConfig->id)."'";
        $tRes = MySqlLegacySupport::getInstance()->query($sAlreadyConnectedQuery);
        if (MySqlLegacySupport::getInstance()->num_rows($tRes) > 0) {
            $bHasContent = true;
        }

        return $bHasContent;
    }
}
