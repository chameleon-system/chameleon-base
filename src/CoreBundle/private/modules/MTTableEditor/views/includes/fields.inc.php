<?php
/** @var TdbCmsTblFieldTab $oTab */
$iTextFieldCount = 0;
$sLastTextFieldName = '';
$data['oFields']->GoToStart();
$sTmpFormTabsContent = '';
while ($oField = $data['oFields']->Next()) {
    /** @var $oField TCMSField */
    if ($sTabId == $oField->oDefinition->sqlData['cms_tbl_field_tab']) {
        if ('hidden' != $oField->GetDisplayType()) {
            $rowColorStyle = '';
            $rowClass = '';
            if (!empty($oField->oDefinition->sqlData['row_hexcolor'])) {
                $rowColorStyle = " style=\"border-right: 5px solid #{$oField->oDefinition->sqlData['row_hexcolor']}; border-left: 5px solid #{$oField->oDefinition->sqlData['row_hexcolor']};\"";
            }

            ++$rowCount;
            if ($oField->completeRow) { // Headline row
                $sTmpFormTabsContent .= "<tr><td colspan=\"2\"{$rowColorStyle}>";
                $sTmpFormTabsContent .= '<div class="fieldSeperator">';
                if (!empty($oField->oDefinition->sqlData['049_helptext'])) {
                    $sTmpFormTabsContent .= '<div id="tooltip'.$oField->name.'" style="float:left;" class="xtoolTipButton"><img src="'.TGlobal::GetPathTheme()."/images/icons/icon_info.gif\" width=\"16\" height=\"16\" alt=\"\" onclick=\"$('#tooltip".$oField->name."_content').toggle();\"></div>&nbsp;&nbsp;";
                }
                $sTmpFormTabsContent .= $oField->GetContent();
                $sTmpFormTabsContent .= '</div>';

                if (!empty($oField->oDefinition->sqlData['049_helptext'])) {
                    $sTmpFormTabsContent .= '<div style="display: none;" id="tooltip'.$oField->name.'_content" class="tooltipContainer">'.nl2br($oField->oDefinition->sqlData['049_helptext']).'</div>';
                }
                $sTmpFormTabsContent .= '</td></tr>';
            } else {
                if (true == $isReadOnly) { // overwrite field type with readonly (e.g. record is locked by another cms user)
                    $oField->oDefinition->sqlData['modifier'] = 'readonly';
                }

                $sTmpFormTabsContent .= '<tr class="'.$rowClass.'">
            <td valign="top" class="leftTD"'.$rowColorStyle.'>
              <div id="fieldname_'.TGlobal::OutHTML($oField->name).'">
            ';

                if (!empty($oField->oDefinition->sqlData['049_helptext'])) {
                    $sTmpFormTabsContent .= '<div id="tooltip'.$oField->name.'" class="toolTipButton"><img src="'.TGlobal::GetPathTheme().'/images/icons/icon_info.gif" width="16" height="16" alt="'.TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.cms_module_table_editor.field_help')).'" title="'.TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.cms_module_table_editor.field_help'))."\" onclick=\"$('#tooltip".$oField->name."_content').toggle();\"></div>\n";
                }

                $oFieldConfig = TdbCmsFieldConf::GetNewInstance();

                $oFieldConfig->Load($oField->oDefinition->id);

                if (ACTIVE_TRANSLATION) {
                    $bIsTextField = false;
                    $aAllowedTypes = array('CMSFIELD_STRING', 'CMSFIELD_TEXT', 'CMSFIELD_STRING_UNIQUE');
                    $oFieldType = $oField->oDefinition->GetFieldType();
                    if (in_array($oFieldType->sqlData['constname'], $aAllowedTypes)) {
                        $bIsTextField = true;
                        $sLastTextFieldName = $oField->name;
                        ++$iTextFieldCount;
                    }

                    if ('1' == $oField->oDefinition->sqlData['is_translatable']) {
                        $sPrefix = TGlobal::GetLanguagePrefix($oTable->GetLanguage());
                        if (empty($sPrefix)) {
                            $sPrefix = $oBaseLanguage->fieldIso6391;
                        }

                        $sTmpFormTabsContent .= '<span class="badge pull-right">'.$sPrefix."</span>\n";

                        // show icon if record is not translated yet (disabled for MLT fields)
                        if ($oFieldType->fieldBaseType = 'standard' && isset($oTable->sqlData[$oFieldConfig->fieldName.'__'.$sPrefix])) {
                            if ('' == $oTable->sqlData[$oFieldConfig->fieldName.'__'.$sPrefix]) {
                                $sTmpFormTabsContent .= '<img class="translationStateIcon" src="'.TGlobal::GetStaticURLToWebLib('/images/icons/comment_edit.png').'" alt="'.TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.cms_module_table_editor.not_translated')).'" title="'.TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.cms_module_table_editor.not_translated')).'" />';
                            }
                        }
                    }
                }
                $sTmpFormTabsContent .= '<div>';

                $sTmpFormTabsContent .= $oFieldConfig->fieldTranslation;

                if ($oField->IsMandatoryField()) {
                    $sTmpFormTabsContent .= '&nbsp;<span class="requiredfield">*<span>';
                }
                $sTmpFormTabsContent .= '</div>
            </div>
    	    </td>
          <td class="rightTD">';
                if (!empty($oField->oDefinition->sqlData['049_helptext'])) {
                    $sTmpFormTabsContent .= '<div style="display: none;" id="tooltip'.$oField->name.'_content" class="tooltipContainer">'.nl2br($oField->oDefinition->sqlData['049_helptext'])."</div>\n";
                }
                $sTmpFormTabsContent .= $oField->GetContent();
                $sTmpFormTabsContent .= '</td>
        </tr>
        ';
            }
        }
    }
}

if (!empty($sTmpFormTabsContent)) {
    if (isset($sTabName)) {
        $sFormTabsTitles .= '<li><a data-fullname="'.TGlobal::OutHTML($sTableTitle.' » '.$sRecordName.' » '.ucfirst($sTabName)).'" href="#tab-'.strtolower(TTools::RealNameToURLName($sTabName)).'">'.$sTabName.'</a></li>';
    } else {
        $sTabName = 'base';
    }
    $sFormTabsContent .= '<div id="tab-'.strtolower(TTools::RealNameToURLName($sTabName)).'">';

    if (isset($sTabName) && $sTabName == TGlobal::Translate('chameleon_system_core.cms_module_table_editor.tab_default')) {
        if ('' != $oTableDefinition->sqlData['notes']) {
            $sFormTabsContent .= '<div class="descriptionContainer">
                                                    <div style="display: none" class="description" id="tabDescription'.$iTabCount.'">
                                                        '.nl2br(TGlobal::OutHTML($oTableDefinition->sqlData['notes'])).'
                                                    </div>
                                              </div>
                                           <img src="'.TGlobal::GetStaticURLToWebLib('/images/icons/bookmark.png').'" name="scrollup" id="scrollup" onClick="$(\'#tabDescription'.$iTabCount.'\').toggle(\'fast\');" style="cursor: hand; cursor: pointer; margin-top: -7px;" />
                                           ';
        }
    }
    if (isset($oTab)) {
        if ('' != $oTab->fieldDescription) {
            $sFormTabsContent .= '<div class="descriptionContainer">
                                                    <div style="display: none" class="description" id="tabDescription'.$iTabCount.'">
                                                        '.nl2br(TGlobal::OutHTML($oTab->fieldDescription)).'
                                                    </div>
                                              </div>
                                           <img src="'.TGlobal::GetStaticURLToWebLib('/images/icons/bookmark.png').'" name="scrollup" id="scrollup" onClick="$(\'#tabDescription'.$iTabCount.'\').toggle(\'fast\');" style="cursor: hand; cursor: pointer; margin-top: -7px;" />
                                           ';
        }
    }
    $sFormTabsContent .= '

        <table class="table table-striped table-hover">
        ';

    $sFormTabsContent .= $sTmpFormTabsContent;
    $sFormTabsContent .= '</table>
      </div>
      ';
}

/** add handling of ENTER key to trigger the save button if we have a form with only one text field */
if (!empty($sLastTextFieldName) && 1 == $iTextFieldCount) {
    ?>
<script type="text/javascript">
    $(document).ready(function () {
        $('#<?=$sLastTextFieldName; ?>').keypress(function (e) {
            code = e.keyCode ? e.keyCode : e.which;
            if (code.toString() == 13) { // ENTER key
                e.preventDefault();
                $('.btn-group button.itemsave').click();
            }
        });
    });
</script>
<?php
}
