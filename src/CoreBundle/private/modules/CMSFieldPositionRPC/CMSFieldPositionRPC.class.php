<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class CMSFieldPositionRPC extends TCMSModelBase
{
    public function &Execute()
    {
        $this->data = parent::Execute();
        $this->GetSortElements();

        return $this->data;
    }

    public function DefineInterface()
    {
        parent::DefineInterface();
        $externalFunctions = array('GetSortElements', 'SavePosChange');
        $this->methodCallAllowed = array_merge($this->methodCallAllowed, $externalFunctions);
    }

    /**
     * generates the list of elements that should be reorderable via ajax.
     *
     * @return string
     */
    protected function GetSortElements()
    {
        $sTableID = $this->global->GetUserData('tableID');
        $sFieldName = $this->global->GetUserData('fieldName');
        $sRecordID = $this->global->GetUserData('recordID');
        $sTableSQLName = $this->global->GetUserData('tableSQLName');
        $sRestriction = $this->global->GetUserData('sRestriction');
        $sRestrictionField = $this->global->GetUserData('sRestrictionField');
        $bRestrictedFieldIsMLTConnection = false;

        $oFieldConf = TdbCmsFieldConf::GetNewInstance();
        $oFieldConf->LoadFromFields(array('name' => $sFieldName, 'cms_tbl_conf_id' => $sTableID));
        $sPermanentRestrictionField = $oFieldConf->GetFieldtypeConfigKey('sPermanentRestrictionField');

        if ($sPermanentRestrictionField) {
            $sRestrictionField = $sPermanentRestrictionField;
        }

        if ($sPermanentRestrictionField && !$bRestrictedFieldIsMLTConnection) {
            $oRecordObject = new TCMSRecord($sTableSQLName, $sRecordID);
            $sRestriction = $oRecordObject->sqlData[$sRestrictionField];
        }

        $sHTML = "<ul id=\"posList\" class=\"list-group\">\n";

        $sHTML .= $this->GetListItems($sTableSQLName, $sRecordID, $sFieldName, $sRestrictionField, $sRestriction, ('cms_field_conf' == $sTableSQLName));

        $sHTML .= "</ul>\n";

        $this->data['list'] = $sHTML;
        $this->data['recordID'] = $sRecordID;
        $this->data['tableSQLName'] = $sTableSQLName;
        $this->data['fieldName'] = $sFieldName;
    }

    /**
     * generates a html string with <li> elements for each field of the table.
     *
     * @param string $sTableSQLName - sql name of the table
     * @param string $sRecordID - the id of the record (position field) in the table
     * @param string $sFieldName - the field name of the record (position field) in the table
     * @param string $sRestrictionField - the field name that should be used as restriction
     * @param string $sRestriction - the value to restrict $sRestrictionField to
     * @param bool $bCmsFieldConfTable - true if the current table is cms_field_conf table (generates fixed li elements for each tab - so the fields are grouped)
     *
     * @return string - the html construct of the <li> elements
     */
    protected function GetListItems($sTableSQLName, $sRecordID, $sFieldName, $sRestrictionField, $sRestriction, $bCmsFieldConfTable)
    {
        $iCount = 0;
        $sHTML = '';

        if ($bCmsFieldConfTable) {
            $sQuery = $this->GetCmsFieldConfListQuery($sRestrictionField, $sRestriction);
        } else {
            $sQuery = $this->GetStandardListQuery($sTableSQLName, $sFieldName, $sRestrictionField, $sRestriction);
        }

        $sClassName = TCMSTableToClass::GetClassName(TCMSTableToClass::PREFIX_CLASS, $sTableSQLName).'List';
        /** @var $oPositionList TCMSRecordList */
        $oPositionList = call_user_func_array(array($sClassName, 'GetList'), array($sQuery, null, false, true, true));

        /** @var $oPositionRow TCMSRecord */
        while ($oPositionRow = $oPositionList->Next()) {
            if ($bCmsFieldConfTable) {
                $sHTML .= $this->GetTab($oPositionRow);
            }

            ++$iCount;

            $activeClass = '';
            if ($oPositionRow->id == $sRecordID) {
                $activeClass = 'active';
            }

            $sHTML .= '<li id="item'.$oPositionRow->id.'" rel="'.$iCount.'" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center '.$activeClass.'">';
            $sHTML .= '<input type="hidden" name="aPosOrder[]" value="'.$oPositionRow->id.'" />'.$oPositionRow->GetName();
            $sHTML .= '<i class="fas fa-arrows-alt-v"></i>';
            $sHTML .= "</li>\n";
        }

        return $sHTML;
    }

    /**
     * this function will only be called if the current table of the position field is the cms_field_conf table.
     *
     * @param TCMSRecord $oPositionRow - the record in the position list
     *
     * @return string - fixed <li> html element with the tab name
     */
    protected function GetTab($oPositionRow)
    {
        static $sTabId = '';
        static $bFirst = true;
        $sHTML = '';

        if ((array_key_exists('cms_tbl_field_tab', $oPositionRow->sqlData) && $oPositionRow->sqlData['cms_tbl_field_tab'] != $sTabId) || true === $bFirst) {
            if (true === $bFirst) {
                $sTabName = TGlobal::Translate('chameleon_system_core.cms_module_table_editor.tab_default');
                $bFirst = false;
            } else {
                $sTabId = $oPositionRow->sqlData['cms_tbl_field_tab'];
                $oTab = TdbCmsTblFieldTab::GetNewInstance($sTabId);
                $sTabName = $oTab->GetName();
            }
            $sHTML = '<li id="item'.$sTabId.'" rel="0" class="list-group-item list-group-item-dark disabled" style="background-color:#8ab9ff; color#000000; font-weight:bold;"> '.TGlobal::Translate('chameleon_system_core.field_mltrpc.tab', array('%tab%' => $sTabName)).'</li> ';
        }

        return $sHTML;
    }

    /**
     * constructs a list query for a position field in a normal table (not cms_field_conf table)
     * and respects restrictions set in field config or given via url - passed as parameters to the function.
     *
     * @param string $sTableSQLName - sql name of the table
     * @param string $sFieldName - the field name of the record (position field) in the table
     * @param string $sRestrictionField - the field name that should be used as restriction
     * @param string $sRestriction - the value to restrict $sRestrictionField to
     *
     * @return string - list query the any standard table (not cms_field_conf table) with all restrictions
     */
    protected function GetStandardListQuery($sTableSQLName, $sFieldName, $sRestrictionField, $sRestriction)
    {
        $sRestrictionTable = '';
        $bRestrictedFieldIsMLTConnection = false;

        if ('_mlt' == substr($sRestrictionField, strlen($sRestrictionField) - 4, strlen($sRestrictionField))) {
            $bRestrictedFieldIsMLTConnection = true;
            $sRestrictionTable = substr($sRestrictionField, 0, strlen($sRestrictionField) - 4);
        }

        $sQuery = 'SELECT *
                   FROM `'.MySqlLegacySupport::getInstance()->real_escape_string($sTableSQLName).'` ';
        if (!empty($sRestrictionField) && !empty($sRestriction)) {
            if ($bRestrictedFieldIsMLTConnection) {
                $sQuery .= ' RIGHT JOIN `'.MySqlLegacySupport::getInstance()->real_escape_string($sRestrictionTable).'_'.MySqlLegacySupport::getInstance()->real_escape_string($sTableSQLName).'_mlt`
                               ON `'.MySqlLegacySupport::getInstance()->real_escape_string($sRestrictionTable).'_'.MySqlLegacySupport::getInstance()->real_escape_string($sTableSQLName).'_mlt`.`target_id`=`'.MySqlLegacySupport::getInstance()->real_escape_string($sTableSQLName).'`.`id`
                            WHERE `'.MySqlLegacySupport::getInstance()->real_escape_string($sRestrictionTable).'_'.MySqlLegacySupport::getInstance()->real_escape_string($sTableSQLName)."_mlt`.`source_id`='".MySqlLegacySupport::getInstance()->real_escape_string($sRestriction)."'";
            } else {
                $sQuery .= ' WHERE `'.MySqlLegacySupport::getInstance()->real_escape_string($sRestrictionField)."` = '".MySqlLegacySupport::getInstance()->real_escape_string($sRestriction)."'";
            }
        }

        $sQuery .= ' ORDER BY `'.MySqlLegacySupport::getInstance()->real_escape_string($sTableSQLName).'`.`'.MySqlLegacySupport::getInstance()->real_escape_string($sFieldName).'` ASC';

        return $sQuery;
    }

    /**
     * constructs a specific list query for the cms_field_conf table
     * and respects restrictions set in field config or given via url - passed as parameters to the function.
     *
     * @param string $sRestrictionField - the field name that should be used as restriction
     * @param string $sRestriction - the value to restrict $sRestrictionField to
     *
     * @return string
     */
    protected function GetCmsFieldConfListQuery($sRestrictionField, $sRestriction)
    {
        $sQuery = 'SELECT `cms_field_conf`.*,
                        `cms_tbl_field_tab`.`position` AS tabpos
                   FROM `cms_field_conf` ';
        $sQuery .= 'LEFT JOIN `cms_tbl_field_tab` ON `cms_tbl_field_tab`.`id` = `cms_field_conf`.`cms_tbl_field_tab`';
        if (!empty($sRestrictionField) && !empty($sRestriction)) {
            $sQuery .= ' WHERE `cms_field_conf`.`'.MySqlLegacySupport::getInstance()->real_escape_string($sRestrictionField)."` = '".MySqlLegacySupport::getInstance()->real_escape_string($sRestriction)."'";
        }
        $sQuery .= ' ORDER BY tabpos ASC, `cms_field_conf`.`position` ASC';

        return $sQuery;
    }

    /**
     * saves a dropped element at the new position and reorders the other elements.
     *
     * @return int - returns null if current record position did not change
     */
    public function SavePosChange()
    {
        $iNewPositionOfCurrentRecord = null;
        $aPosOrder = $this->global->GetUserData('aPosOrder');
        $sFieldName = $this->global->GetUserData('fieldName');
        $sTableSQLName = $this->global->GetUserData('tableSQLName');
        $movedItemID = $this->global->GetUserData('movedItemID');

        $sActiveItemId = $this->global->GetUserData('activeItemId');

        $sTableID = TTools::GetCMSTableId($sTableSQLName);
        $oEditor = TTools::GetTableEditorManager($sTableSQLName, $movedItemID);

        $query = "SELECT *
                  FROM `cms_field_conf`
                 WHERE `cms_tbl_conf_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($sTableID)."'
                   AND `name` = '".MySqlLegacySupport::getInstance()->real_escape_string($sFieldName)."'";
        $result = MySqlLegacySupport::getInstance()->query($query);
        if (MySqlLegacySupport::getInstance()->num_rows($result) > 0) {
            $iNewPositionOfCurrentRecord = $oEditor->oTableEditor->UpdatePositionField($sFieldName);
            $sClassName = TCMSTableToClass::GetClassName(TCMSTableToClass::PREFIX_CLASS, $sTableSQLName);

            /**
             * @var TCMSRecord $oRecord
             */
            $oRecord = new $sClassName();
            $oRecord->SetEnableObjectCaching(false);
            if ($oRecord->Load($sActiveItemId)) {
                $iNewPositionOfCurrentRecord = $oRecord->sqlData[$sFieldName];
            }
        }

        return $iNewPositionOfCurrentRecord;
    }

    public function GetHtmlHeadIncludes()
    {
        $aIncludes = parent::GetHtmlHeadIncludes();
        $fieldName = $this->global->GetUserData('fieldName');

        $aIncludes[] = '<script src="'.TGlobal::GetStaticURLToWebLib('/javascript/jquery-ui-1.12.1.custom/jquery-ui.js').'" type="text/javascript"></script>';
        $aIncludes[] = '<link href="'.TGlobal::GetStaticURLToWebLib('/iconFonts/fontawesome-free-5.8.1/css/all.css').'" media="screen" rel="stylesheet" type="text/css" />';
        $aIncludes[] = '<link href="'.TGlobal::GetPathTheme().'/css/tableeditcontainer.css" rel="stylesheet" type="text/css" />';
        $aIncludes[] = '<script type="text/javascript">
      $(document).ready(function(){
        $("#posList").sortable({
          smooth: false,
          tolerance: "fit",
          activeClass: "active",
          containment: "document",
          axis: "y",
          scroll: "true",
          helper: "clone",
          items: "li:not(.disabled)",
          start:  function(e , el){
            $(el.helper).addClass("active");
            helperforthis = el.helper;
          },
          update: function (e , el) {
            var id = $(helperforthis).attr("id").replace("item", "");
            $("#movedItemID").val(id);

            PostAjaxForm("poslistform", sortAjaxCallback);
          }
        });
      });

      function sortAjaxCallback(data) {
        parent.document.cmseditform.'.TGlobal::OutJS($fieldName).'.value = data;
        CloseModalIFrameDialog();
      }
      </script>';

        return $aIncludes;
    }
}
