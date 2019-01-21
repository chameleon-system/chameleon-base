<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\Util\MltFieldUtil;
use ChameleonSystem\DatabaseMigration\DataModel\LogChangeDataModel;
use ChameleonSystem\DatabaseMigration\Query\MigrationQueryData;

/**
 * Treemanagement Module for the CMS Navigation tree.
/**/
class CMSFieldMLTRPC extends TCMSModelBase
{
    public $rpcData = null;
    public $mltTable = null;
    public $sourceTable = null;
    public $sourceID = null;
    public $targetID = null;

    public function Init()
    {
        parent::Init();

        $this->mltTable = $this->global->GetUserData('mltTable');
        $this->sourceID = $this->global->GetUserData('sourceID');
        $this->targetID = $this->global->GetUserData('targetID');
        $this->sourceTable = $this->global->GetUserData('sourceTable');
    }

    public function &Execute()
    {
        $rpcAction = $this->global->GetUserData('action');

        $this->data['returnData'] = '';

        if ('remove' == $rpcAction) {
            $this->data['returnData'] = $this->removeConnection();
        } elseif ('assign' == $rpcAction) {
            $this->data['returnData'] = $this->assignConnection();
        }

        return $this->data;
    }

    public function DefineInterface()
    {
        parent::DefineInterface();
        $externalFunctions = array('assignConnection', 'removeConnection', 'GetSortElements', 'SavePosChange');
        $this->methodCallAllowed = array_merge($this->methodCallAllowed, $externalFunctions);
    }

    /**
     * removes connections between MLT connected tables.
     */
    public function removeConnection()
    {
        $returnVal = false;
        if (!empty($this->mltTable) && !empty($this->sourceID) && !empty($this->targetID) && !empty($this->sourceTable)) {
            $oTableConf = TdbCmsTblConf::GetNewInstance();
            $oTableConf->LoadFromField('name', $this->sourceTable);

            $targetField = substr($this->mltTable, strlen($this->sourceTable) + 1);

            $sTargetID = $this->targetID;
            $oTableEditor = new TCMSTableEditorManager();
            $oTableEditor->Init($oTableConf->id, $this->sourceID);
            $oTableEditor->RemoveMLTConnection($targetField, $sTargetID);

            $returnVal = new stdClass();
            $returnVal->fieldName = $targetField;
        }

        return $returnVal;
    }

    /**
     * assigns connections between MLT connected tables.
     */
    public function assignConnection()
    {
        $returnVal = false;
        if (!empty($this->mltTable) && !empty($this->sourceID) && !empty($this->targetID) && !empty($this->sourceTable)) {
            $oTableConf = TdbCmsTblConf::GetNewInstance();
            $oTableConf->LoadFromField('name', $this->sourceTable);
            $targetField = substr($this->mltTable, strlen($this->sourceTable) + 1);

            $sTargetID = $this->targetID;
            $oTableEditor = new TCMSTableEditorManager();
            $oTableEditor->Init($oTableConf->id, $this->sourceID);
            $oTableEditor->AddMLTConnection($targetField, $sTargetID);
            $returnVal = new stdClass();
            $returnVal->fieldName = $targetField;
        }

        return $returnVal;
    }

    /**
     * Get Last sort number for source.
     *
     * @return int
     */
    protected function GetMLTSortNumber()
    {
        $iSortNumber = 0;
        $sQuery = 'SELECT `entry_sort` FROM `'.MySqlLegacySupport::getInstance()->real_escape_string($this->mltTable)."`
                 WHERE `source_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->sourceID)."'
                 ORDER BY `entry_sort` DESC
                 LIMIT 1";
        $res = MySqlLegacySupport::getInstance()->query($sQuery);
        if (MySqlLegacySupport::getInstance()->num_rows($res) > 0) {
            $aRow = MySqlLegacySupport::getInstance()->fetch_assoc($res);
            $iSortNumber = $aRow['entry_sort'] + 1;
        }

        return $iSortNumber;
    }

    /**
     * Returns the name of the MLt field without source table name.
     * Postfix _mlt was filtered.
     *
     * @return string
     */
    protected function GetFieldMltName()
    {
        $sTableMltName = $this->global->GetUserData('tableSQLName');
        $sFieldMltName = $this->global->GetUserData('field');
        if (!empty($sFieldMltName)) {
            $mltFieldUtil = $this->getMltFieldUtil();
            $sFieldMltName = $mltFieldUtil->cutMltExtension($sFieldMltName);
            $cleanMltFieldName = $mltFieldUtil->cutMultiMltFieldNumber($sFieldMltName);
            if ($cleanMltFieldName != $sTableMltName) {
                $sTableMltName = $sFieldMltName.'_'.$sTableMltName;
            } else {
                $sTableMltName = $sFieldMltName;
            }
        }

        return $sTableMltName;
    }

    /**
     * Returns the mlt table name.
     *
     * @return string
     */
    protected function GetMLTTableName()
    {
        $sFieldMltName = $this->GetFieldMltName();
        $sRestrictionField = $this->global->GetUserData('sRestrictionField');
        $sMLTTableName = substr($sRestrictionField, 0, -4).'_'.$sFieldMltName.'_mlt';

        return $sMLTTableName;
    }

    /**
     * Get all connected records and table informations.
     */
    public function GetSortElements()
    {
        $sTableSQLName = $this->global->GetUserData('tableSQLName');
        $sRestriction = $this->global->GetUserData('sRestriction');
        $sRestrictionField = $this->global->GetUserData('sRestrictionField');
        if ('_mlt' == substr($sRestrictionField, -4, 4)) {
            $sRestrictionField = substr($sRestrictionField, 0, -4);
        }
        $sMltTableName = $this->GetMLTTableName();

        $sReturnData = "<ul id=\"posList\" class=\"posListUL\">\n";

        $oTableConf = TdbCmsTblConf::GetNewInstance();
        $oTableConf->LoadFromField('name', $sTableSQLName);
        $listClass = null;
        $oListManager = $oTableConf->GetListObject($listClass);
        $sQuery = $oListManager->FilterQuery();
        $oPositionList = new TCMSRecordList('TCMSRecord', $sTableSQLName, $sQuery);
        $oPositionList->ChangeOrderBy(array("`{$sMltTableName}`.`entry_sort`" => 'ASC'));

        $count = 0;
        $sTabId = '';
        $oTab = TdbCmsTblFieldTab::GetNewInstance();

        while ($oPositionRow = $oPositionList->Next()) { //@var $oFile TCMSRecord
            if (0 == $count) {
                $sReturnData .= $this->GetRecordDataRow($sTableSQLName, $oPositionRow, $count);
            }
            ++$count;
            $activeRecord = 'positionList';
            $sReturnData .= '<li id="item'.$oPositionRow->id.'" rel="'.$count."\" class=\"{$activeRecord}\"><input type=\"hidden\" name=\"aPosOrder[]\" value=\"".$oPositionRow->id.'" />'.$this->GetRecordDataRow($sTableSQLName, $oPositionRow, $count)."</li>\n";
        }

        $sReturnData .= "</ul>\n";

        $this->data['list'] = $sReturnData;
        $this->data['tableSQLName'] = $sTableSQLName;
        $this->data['sMltTableName'] = $sMltTableName;
        $this->data['sSourcerecordId'] = $sRestriction;
        $this->data['sTargetTable'] = $sRestrictionField;
    }

    /**
     * get record data row as string.
     *
     * @param string     $sTableSQLName
     * @param TCMSRecord $oRecord
     * @param int        $sCount
     *
     * @return string
     */
    protected function GetRecordDataRow($sTableSQLName, $oRecord, $sCount)
    {
        $oTableConf = TdbCmsTblConf::GetNewInstance();
        $oTableConf->LoadFromField('name', $sTableSQLName);
        $oTableEditorManager = new TCMSTableEditorManager();
        /** @var $oTableEditor TCMSTableEditorManager */
        $oTableEditorManager->Init($oTableConf->id, $oRecord->sqlData['id']);
        $oShowPropertiesList = $oTableConf->GetFieldPropertyListFieldsList();
        if (0 == $sCount) {
            $sDataRow = $this->WriteFieldInfoLine($oShowPropertiesList, $oRecord, $oTableConf);
        } else {
            $sDataRow = $this->ListFieldsToString($oShowPropertiesList, $oRecord, $oTableConf);
        }

        return $sDataRow;
    }

    /**
     * write info line for all fields which should be in sort list as div.
     *
     * @param TCMSListFieldList $oListFieldsList
     * @param TCMSRecord        $oRecord
     * @param TCMSTblConf       $oTableConf
     *
     * @return string
     */
    protected function WriteFieldInfoLine($oListFieldsList, $oRecord, $oTableConf)
    {
        $sFieldListString = '<div class=infoline style=\"position:relative;font-size:0px;height:0px;\">';
        $bFindShowField = false;
        if (!is_null($oListFieldsList) && $oListFieldsList->Length() > 0) {
            while ($oListField = &$oListFieldsList->Next()) {
                if ($oListField->fieldShowInSort) {
                    if (!empty($oListField->fieldTitle)) {
                        $sFieldListString .= '<div id="'.$oListField->fieldDbAlias.'" class="infolineitem" style="display:none;position:absolute;background-color:#FFFFFF;border:1px solid #A9C4E7;padding:3px;">'.$oListField->fieldTitle.'</div>';
                        $bFindShowField = true;
                    } else {
                        $sFieldListString .= '<div id="'.$oListField->fieldDbAlias.'" class="infolineitem" style="display:none;position:absolute;background-color:#FFFFFF;border:1px solid #A9C4E7;padding:3px;">'.$oListField->fieldDbAlias.'</div>';
                        $bFindShowField = true;
                    }
                }
            }
        }
        if (false == $bFindShowField) {
            $sFieldListString .= '<div id="getname" class="infolineitem" style="display:none;position:absolute;background-color:#FFFFFF;border:1px solid #A9C4E7;padding:3px;">'.TGlobal::Translate('chameleon_system_core.field_mltrpc.name').'</div>';
        }

        return $sFieldListString.'</div>';
    }

    /**
     * Get all field values value as table.
     *
     * @param TCMSListFieldList $oListFieldsList
     * @param TCMSRecord        $oRecord
     * @param TCMSTblConf       $oTableConf
     *
     * @return string
     */
    protected function ListFieldsToString($oListFieldsList, $oRecord, $oTableConf)
    {
        $sFieldListString = '<table cellpadding="3"><tr>';
        $bFindShowField = false;
        if (!is_null($oListFieldsList) && $oListFieldsList->Length() > 0) {
            while ($oListField = &$oListFieldsList->Next()) {
                if ($oListField->fieldShowInSort) {
                    if (empty($oListField->fieldCallbackFnc)) {
                        $sFieldListString .= "<td valign=\"top\" align=\"center\" class=\"$oListField->fieldDbAlias\">".$oRecord->sqlData[$oListField->fieldDbAlias].'</td>';
                        $bFindShowField = true;
                    } else {
                        if ($oListField->fieldUseCallback) {
                            $sFieldListString .= "<td valign=\"top\" align=\"center\" class=\"$oListField->fieldDbAlias\">".call_user_func($oListField->fieldCallbackFnc, $oRecord->sqlData[$oListField->fieldDbAlias], $oRecord->sqlData, $oListField->fieldTitle).'</td>';
                            $bFindShowField = true;
                        }
                    }
                }
            }
        }
        if (false == $bFindShowField) {
            $sFieldListString .= '<td valign="top" align="center" class="getname">'.$oRecord->GetName().'</td>';
        }

        return $sFieldListString.'</tr></table>';
    }

    public function GetHtmlHeadIncludes()
    {
        $aIncludes = parent::GetHtmlHeadIncludes();
        $sMltFieldName = $this->global->GetUserData('field').'_iframe';

        $aIncludes[] = '<script src="'.TGlobal::GetStaticURLToWebLib('/javascript/jquery-ui-1.12.1.custom/jquery-ui.js').'" type="text/javascript"></script>';
        $aIncludes[] = '<link href="'.TGlobal::GetPathTheme().'/css/tableeditcontainer.css" rel="stylesheet" type="text/css" />';
        $sJavaString = '<script type="text/javascript">
      $(document).ready(function(){
        $(".positionList td ").hover(
        function(){
          //class = this.className;
          offset=$(this).offset();
          width=$(this).width();
          $("#"+this.className).css({"top":(offset.top-1)+"px","left" : (offset.left+width+10)+"px"});
          $("#"+this.className).show();
        },
        function(){
          $("#"+this.className).hide();
        });
        $("#posList").sortable({
          smooth:	false,
          tolerance: "fit",
          activeClass: "positionListActive",
          containment: "document",
          axis: "y",
          scroll: "true",
          items: "li:not(.disabled)",
          start:	function(e , el){
            $(".positionList td ").hover(function(){},function(){});
            $(".infolineitem").hide();
            helper=el.helper;
            $(el.helper).removeClass("positionList");
            $(el.helper).addClass("positionListActive");
            $(el.helper).css("width",$("#posList .positionList").width() +"px");
          },
          update: function (e , el) {
            // $("#posList").droppableDestroy();

            var id = $(helper).attr("id").replace("item", "");
            $("#movedItemID").val(id);
            $(".positionList td ").hover(function(){},function(){});
            PostAjaxForm("poslistform", sortAjaxCallback);
          }
        });
      });

      function sortAjaxCallback() {
        CloseModalIFrameDialog();';
        if (!empty($sMltFieldName)) {
            $sJavaString .= 'parent.document.getElementById("'.$sMltFieldName.'").contentWindow.location.reload(true);';
        }
        $sJavaString .= '
      }
      </script>';
        $aIncludes[] = $sJavaString;

        return $aIncludes;
    }

    /**
     * saves a dropped element at the new position and reorders the other elements.
     *
     * @return bool
     */
    public function SavePosChange()
    {
        $aPosOrder = $this->global->GetUserData('aPosOrder');
        $tableSQLName = $this->global->GetUserData('tableSQLName');
        $movedItemID = $this->global->GetUserData('movedItemID');
        $sMltTableName = $this->global->GetUserData('sMltTableName');
        $sSourcerecordId = $this->global->GetUserData('sSourcerecordId');
        $sTargetTable = $this->global->GetUserData('sTargetTable');

        $pos = 0;
        $aQuery = array();
        TCacheManager::PerformeTableChange($sTargetTable, $sSourcerecordId);
        $editLanguage = $this->getLanguageService()->getActiveEditLanguage();
        foreach ($aPosOrder as $id) {
            ++$pos;
            $updateQuery = 'UPDATE `'.MySqlLegacySupport::getInstance()->real_escape_string($sMltTableName)."`
                           SET `entry_sort` = '{$pos}'
                         WHERE `source_id` = '{$sSourcerecordId}'
                           AND `target_id` = '{$id}'";
            MySqlLegacySupport::getInstance()->query($updateQuery);
            TCacheManager::PerformeTableChange($tableSQLName, $id);

            $migrationQueryData = new MigrationQueryData($sMltTableName, $editLanguage->fieldIso6391);
            $migrationQueryData
                ->setFields(array(
                    'entry_sort' => $pos,
                ))
                ->setWhereEquals(array(
                    'source_id' => $sSourcerecordId,
                    'target_id' => $id,
                ))
            ;
            $aQuery[] = new LogChangeDataModel($migrationQueryData, LogChangeDataModel::TYPE_UPDATE);
        }

        TCMSLogChange::WriteTransaction($aQuery);

        return true;
    }

    /**
     * @return MltFieldUtil
     */
    protected function getMltFieldUtil()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.util.mlt_field');
    }

    /**
     * @return \ChameleonSystem\CoreBundle\Service\LanguageServiceInterface
     */
    private function getLanguageService()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.language_service');
    }
}
