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
use ChameleonSystem\CoreBundle\Util\FieldTranslationUtil;
use ChameleonSystem\CoreBundle\Util\UrlUtil;
use ChameleonSystem\DatabaseMigration\DataModel\LogChangeDataModel;
use ChameleonSystem\DatabaseMigration\Query\MigrationQueryData;
use ChameleonSystem\SecurityBundle\Service\SecurityHelperAccess;

/**
 * lookup.
 * /**/
class TCMSFieldModuleInstance extends TCMSFieldExtendedLookup
{
    /**
     * the module instance data.
     */
    public ?TCMSTPLModuleInstance $oModuleInstance = null;

    /**
     * the module of the module instance.
     */
    public ?TCMSTPLModule $oModule;

    public function GetHTML()
    {
        $this->bShowSwitchToRecord = false;
        $this->LoadModuleInstanceData();

        $html = parent::GetHTML();
        $html .= "<div class=\"cleardiv\">&nbsp;</div>\n\n";

        $aData['oField'] = $this;

        $sRestrictToModule = $this->oDefinition->GetFieldtypeConfigKey('moduleclass');
        $databaseConnection = $this->getDatabaseConnection();
        $nameField = $this->getFieldTranslationUtil()->getTranslatedFieldName('cms_tpl_module', 'name');
        $quotedNameField = $databaseConnection->quoteIdentifier($nameField);
        if (empty($sRestrictToModule)) {
            $sQuery = "SELECT `cms_tpl_module`.*
                   FROM `cms_tpl_module`
                  WHERE `cms_tpl_module`.`show_in_template_engine` = '1'
               ORDER BY `cms_tpl_module`.$quotedNameField ASC";
        } else {
            $quotedRestrictToModule = $databaseConnection->quote($sRestrictToModule);
            $sQuery = "SELECT `cms_tpl_module`.*
                   FROM `cms_tpl_module`
                  WHERE `cms_tpl_module`.`classname` = $quotedRestrictToModule
               ORDER BY `cms_tpl_module`.$quotedNameField ASC";
        }
        $oModuleList = TdbCmsTplModuleList::GetList($sQuery);

        $aData['oModuleList'] = $oModuleList;
        $aData['sAjaxURL'] = $this->GenerateAjaxURL();
        $aData['sModuleView'] = $this->oTableRow->sqlData[$this->name.'_view'];
        $aData['sRecordName'] = $this->oTableRow->GetName();
        $aData['sPageEditURL'] = $this->GeneratePageEditURL();
        $aData['securityHelper'] = $this->getSecurityHelperAccess();

        $oTemplateParser = new TViewParser();
        $oTemplateParser->bShowTemplatePathAsHTMLHint = false;
        $oTemplateParser->AddVarArray($aData);
        $html .= $oTemplateParser->RenderObjectView('moduleInstanceChooser', 'TCMSFields/TCMSFieldModuleInstance');

        return $html;
    }

    protected function GeneratePageEditURL()
    {
        return PATH_CMS_CONTROLLER.$this->getUrlUtil()->getArrayAsUrl(['pagedef' => 'templateengine', '_mode' => 'edit_content'], '?', '&');
    }

    /**
     * sets methods that are allowed to be called via URL (ajax call).
     */
    protected function DefineInterface()
    {
        parent::DefineInterface();
        $externalFunctions = ['RenameInstance', 'CreateNewInstance', 'DeleteInstance'];
        $this->methodCallAllowed = array_merge($this->methodCallAllowed, $externalFunctions);
    }

    protected function GetExtendedListButtons()
    {
        $oTableConf = new TCMSTableConf();
        $oTableConf->LoadFromField('name', 'cms_tpl_module_instance');
        $html = TCMSRender::DrawButton(ServiceLocator::get('translator')->trans('chameleon_system_core.template_engine.select_instance'), 'javascript:'.$this->_GetOpenWindowJS($oTableConf).'', 'far fa-check-square', 'float-left');
        $html .= TCMSRender::DrawButton(ServiceLocator::get('translator')->trans('chameleon_system_core.template_engine.reset_spot'), "javascript:ResetModuleInstance('".TGlobal::OutJS($this->name)."','".TGlobal::OutJS($this->oDefinition->sqlData['field_default_value'])."')", 'fas fa-undo', 'float-left button-spacing');
        $html .= TCMSRender::DrawButton(ServiceLocator::get('translator')->trans('chameleon_system_core.template_engine.create_or_edit_instance'), '#', 'fas fa-plus', 'float-left button-spacing', null, $this->name.'NewInstanceButton');

        return $html;
    }

    protected function _GetOpenWindowJS($oPopupTableConf)
    {
        $sRestrictToModule = $this->oDefinition->GetFieldtypeConfigKey('moduleclass');
        $url = PATH_CMS_CONTROLLER.'?pagedef=extendedLookupListInstances&amp;id='.urlencode($oPopupTableConf->id).'&amp;fieldName='.urlencode($this->name);
        if (!empty($sRestrictToModule)) {
            $url .= '&amp;sModuleRestriction='.urlencode($sRestrictToModule);
        }

        return "CreateModalIFrameDialogCloseButton('".TGlobal::OutHTML($url)."');";
    }

    public function GetConnectedTableName()
    {
        return 'cms_tpl_module_instance';
    }

    public function _GetHTMLValue()
    {
        if (!empty($this->data)) {
            $tblName = 'cms_tpl_module_instance';
            $oRecord = new TCMSRecord();
            $oRecord->table = $tblName;
            $oRecord->Load($this->data);

            $returnValue = $oRecord->GetDisplayValue();
        } else {
            $returnValue = ServiceLocator::get('translator')->trans('chameleon_system_core.field_lookup.nothing_selected');
        }

        return $returnValue;
    }

    /**
     * loads the module instance data.
     */
    protected function LoadModuleInstanceData()
    {
        if (!empty($this->data)) {
            $this->oModuleInstance = new TCMSTPLModuleInstance();
            $this->oModuleInstance->Load($this->data);
            $this->oModule = new TCMSTPLModule();
            if (false !== $this->oModuleInstance->sqlData) {
                $this->oModule->Load($this->oModuleInstance->sqlData['cms_tpl_module_id']);
            } else {
                $this->oModule = null;
                $this->oModuleInstance = null;
            }
        }
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
        $aIncludes[] = '<script src="'.TGlobal::GetStaticURLToWebLib('/javascript/jquery-ui-1.12.1.custom/jquery-ui.js').'" type="text/javascript"></script>';
        $aIncludes[] = '<link href="'.TGlobal::GetStaticURLToWebLib('/css/modules/templateengineeditor.css').'" rel="stylesheet" type="text/css" />';
        $aIncludes[] = '<script src="'.TGlobal::GetStaticURLToWebLib('/javascript/modules/TemplateEngine.js').'" type="text/javascript"></script>';

        return $aIncludes;
    }

    /**
     * changes an existing field definition (alter table).
     *
     * @param string $sOldName
     * @param string $sNewName
     * @param array $postData
     */
    public function ChangeFieldDefinition($sOldName, $sNewName, $postData = null)
    {
        parent::ChangeFieldDefinition($sOldName, $sNewName, $postData);

        $sComment = '';
        if (is_array($postData)) {
            $sComment = substr($postData['translation'].': '.$postData['049_helptext'], 0, 255);
        }
        $query = 'ALTER TABLE `'.MySqlLegacySupport::getInstance()->real_escape_string($this->sTableName).'`
                     CHANGE `'.MySqlLegacySupport::getInstance()->real_escape_string($sOldName).'_view`
                            `'.MySqlLegacySupport::getInstance()->real_escape_string($sNewName)."_view` VARCHAR(255) NOT NULL DEFAULT 'standard'";
        $query .= " COMMENT '".MySqlLegacySupport::getInstance()->real_escape_string($sComment)."'";

        MySqlLegacySupport::getInstance()->query($query);
        $aQuery = [new LogChangeDataModel($query)];

        TCMSLogChange::WriteTransaction($aQuery);
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
     * drop a field definition (alter table).
     */
    public function DeleteFieldDefinition()
    {
        parent::DeleteFieldDefinition();

        $this->RemoveFieldIndex();
        $query = 'ALTER TABLE `'.MySqlLegacySupport::getInstance()->real_escape_string($this->sTableName).'`
                       DROP `'.MySqlLegacySupport::getInstance()->real_escape_string($this->name.'_view').'` ';

        MySqlLegacySupport::getInstance()->query($query);
        $aQuery = [new LogChangeDataModel($query)];
        TCMSLogChange::WriteTransaction($aQuery);
    }

    /**
     * called on the OLD field if the field type is changed (before deleting related tables or droping the index).
     */
    public function ChangeFieldTypePreHook()
    {
        $query = 'ALTER TABLE `'.MySqlLegacySupport::getInstance()->real_escape_string($this->sTableName).'`
                       DROP `'.MySqlLegacySupport::getInstance()->real_escape_string($this->name.'_view').'` ';

        MySqlLegacySupport::getInstance()->query($query);
        $aQuery = [new LogChangeDataModel($query)];
        TCMSLogChange::WriteTransaction($aQuery);
    }

    /**
     * called on the NEW field if the field type is changed (BEFORE anything else is done).
     */
    public function ChangeFieldTypePostHook()
    {
        $query = 'ALTER TABLE `'.MySqlLegacySupport::getInstance()->real_escape_string($this->sTableName).'`
                        ADD `'.MySqlLegacySupport::getInstance()->real_escape_string($this->name)."_view` VARCHAR(255) NOT NULL DEFAULT 'standard'";
        $aQuery = [new LogChangeDataModel($query)];

        TCMSLogChange::WriteTransaction($aQuery);
        MySqlLegacySupport::getInstance()->query($query);
    }

    /**
     * renames the module instance.
     *
     * @return false|TCMSstdClass
     */
    public function RenameInstance()
    {
        $oTdbCmsTplModuleInstance = TdbCmsTplModuleInstance::GetNewInstance();
        $oTableConf = $oTdbCmsTplModuleInstance->GetTableConf();

        if (null === $oTableConf) {
            return false;
        }

        $oTableEditor = new TCMSTableEditorModuleInstance();
        $oTableEditor->Init($oTableConf->id, $this->data);

        return $oTableEditor->RenameInstance();
    }

    public function CreateNewInstance()
    {
        $returnVal = false;
        $oGlobal = TGlobal::instance();
        if ($oGlobal->UserDataExists('moduleID') && $oGlobal->UserDataExists('sView') && $oGlobal->UserDataExists('sName')) {
            $oTdbCmsTplModuleInstance = TdbCmsTplModuleInstance::GetNewInstance();
            $oTableConf = $oTdbCmsTplModuleInstance->GetTableConf();

            $oTableEditor = new TCMSTableEditorModuleInstance();
            $oTableEditor->Init($oTableConf->id);

            $postData = [];
            $postData['name'] = $oGlobal->GetUserData('sName');
            $postData['cms_tpl_module_id'] = $oGlobal->GetUserData('moduleID');
            $postData['template'] = $oGlobal->GetUserData('sView');

            $returnVal = $oTableEditor->Save($postData);
        }

        return $returnVal;
    }

    public function DeleteInstance()
    {
        $returnVal = false;

        $oGlobal = TGlobal::instance();
        if ($oGlobal->UserDataExists('moduleInstanceId')) {
            $moduleInstanceId = $oGlobal->GetUserData('moduleInstanceId');
            $query = "SELECT * FROM `cms_tpl_page_cms_master_pagedef_spot` WHERE `cms_tpl_module_instance_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($moduleInstanceId)."'";
            $result = MySqlLegacySupport::getInstance()->query($query);
            if (MySqlLegacySupport::getInstance()->num_rows($result) > 0) {
                $returnVal = [];

                while ($row = MySqlLegacySupport::getInstance()->fetch_assoc($result)) {
                    $oTdbCmsTplPage = TdbCmsTplPage::GetNewInstance();
                    $oTdbCmsTplPage->Load($row['cms_tpl_page_id']);

                    $oRecordData = new TCMSstdClass();
                    $oRecordData->id = $row['cms_tpl_page_id'];
                    $oRecordData->name = $oTdbCmsTplPage->GetName();
                    $oRecordData->tree = $oTdbCmsTplPage->fieldTreePathSearchString;

                    $returnVal[] = $oRecordData;
                }
            } else {
                $oTdbCmsTplModuleInstance = TdbCmsTplModuleInstance::GetNewInstance();
                $oTableConf = $oTdbCmsTplModuleInstance->GetTableConf();

                $oTableEditor = new TCMSTableEditorModuleInstance();
                $oTableEditor->Init($oTableConf->id, $moduleInstanceId);
                $oTableEditor->Delete($moduleInstanceId);

                $returnVal = $this->name;
            }
        }

        return $returnVal;
    }

    /**
     * called on each field after the record is saved (NOT on insert, only on save).
     *
     * @param string $iRecordId - the id of the record
     */
    public function PostSaveHook($iRecordId)
    {
        $oGlobal = TGlobal::instance();
        $sView = $oGlobal->GetUserData($this->name.'_view');
        $query = 'UPDATE `'.MySqlLegacySupport::getInstance()->real_escape_string($this->sTableName).'`
                        SET `'.MySqlLegacySupport::getInstance()->real_escape_string($this->name)."_view` = '".MySqlLegacySupport::getInstance()->real_escape_string($sView)."'
                        WHERE `id` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->recordId)."'";
        MySqlLegacySupport::getInstance()->query($query);

        $editLanguageIsoCode = $this->getBackendSession()->getCurrentEditLanguageIso6391();
        $migrationQueryData = new MigrationQueryData($this->sTableName, $editLanguageIsoCode);
        $migrationQueryData
            ->setFields([
                $this->name.'_view' => $sView,
            ])
            ->setWhereEquals([
                'id' => $this->recordId,
            ])
        ;
        $aQuery = [new LogChangeDataModel($migrationQueryData, LogChangeDataModel::TYPE_UPDATE)];
        TCMSLogChange::WriteTransaction($aQuery);
    }

    /**
     * {@inheritdoc}
     */
    public function RenderFieldMethodsString()
    {
        $aMethodData = $this->GetFieldMethodBaseDataArray();
        $aMethodData['sMethodName'] = $this->GetFieldMethodName();
        $aMethodData['sReturnType'] = TCMSTableToClass::GetClassName(TCMSTableToClass::PREFIX_CLASS, $this->GetConnectedTableName());

        $sCode = '';

        if (!empty($aMethodData['sReturnType'])) {
            $aMethodData['sClassName'] = $aMethodData['sReturnType'];
            $aMethodData['sClassSubType'] = 'CMSDataObjects';

            $query = "SELECT * FROM cms_tbl_conf where `name` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->GetConnectedTableName())."'";
            $aTargetTable = MySqlLegacySupport::getInstance()->fetch_assoc(MySqlLegacySupport::getInstance()->query($query));

            $aMethodData['sClassType'] = $aTargetTable['dbobject_type'] ?? '';

            $oViewParser = new TViewParser();
            $oViewParser->bShowTemplatePathAsHTMLHint = false;
            $oViewParser->AddVarArray($aMethodData);

            $sMethodCode = $oViewParser->RenderObjectView('getobject', 'TCMSFields/TCMSFieldModuleInstance');
            $oViewParser->AddVar('sMethodCode', $sMethodCode);

            $sCode = $oViewParser->RenderObjectView('method', 'TCMSFields/TCMSField');
        }

        return $sCode;
    }

    private function getFieldTranslationUtil(): FieldTranslationUtil
    {
        return ServiceLocator::get('chameleon_system_core.util.field_translation');
    }

    private function getSecurityHelperAccess(): SecurityHelperAccess
    {
        return ServiceLocator::get(SecurityHelperAccess::class);
    }

    private function getUrlUtil(): UrlUtil
    {
        return ServiceLocator::get('chameleon_system_core.util.url');
    }
}
