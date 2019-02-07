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
            if (!empty($oField->oDefinition->sqlData['row_hexcolor'])) {
                $rowColorStyle = " style=\"border-right: 5px solid #{$oField->oDefinition->sqlData['row_hexcolor']}; border-left: 5px solid #{$oField->oDefinition->sqlData['row_hexcolor']};\"";
            }

            ++$rowCount;
            if ($oField->completeRow) { // Headline row
                $sTmpFormTabsContent .= "<tr><td colspan=\"2\"{$rowColorStyle}>";
                $sTmpFormTabsContent .= '<div class="fieldSeperator">';
                $sTmpFormTabsContent .= $oField->GetContent();
                if ('' !== $oField->oDefinition->sqlData['049_helptext']) {
                    $sTmpFormTabsContent .= '<span class="help-text-button" data-helptextId="'.TGlobal::OutHTML($oField->name).'">
                        <i class="fas fa-info-circle" title="'.TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.cms_module_table_editor.field_help')).'"></i>
                    </span>';
                }
                $sTmpFormTabsContent .= '</div>';

                if ('' !== $oField->oDefinition->sqlData['049_helptext']) {
                    $sTmpFormTabsContent .= '<div class="helptextContainer alert alert-info" id="helptext-'.TGlobal::OutHTML($oField->name).'">'.TGlobal::OutHTML(nl2br($oField->oDefinition->sqlData['049_helptext'])).'</div>';
                }
                $sTmpFormTabsContent .= '</td></tr>';
            } else {
                if (true == $isReadOnly) { // overwrite field type with readonly (e.g. record is locked by another cms user)
                    $oField->oDefinition->sqlData['modifier'] = 'readonly';
                }


                // todo $rowColorStyle
                $sTmpFormTabsContent .= '
                <div class="form-group table-editor-form-group row" id="fieldname_'.TGlobal::OutHTML($oField->name).'">
                    <div class="col-sm-2 col-form-label">
            ';

                $oFieldConfig = TdbCmsFieldConf::GetNewInstance();
                $oFieldConfig->Load($oField->oDefinition->id);

                if (!empty($oField->oDefinition->sqlData['049_helptext'])) {
                    $sTmpFormTabsContent .= '<span class="float-right help-text-button mt-1" data-helptextId="'.TGlobal::OutHTML($oField->name).'">
                        <i class="fas fa-info-circle" title="'.TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.cms_module_table_editor.field_help')).'"></i>
                    </span>';
                }

                if (ACTIVE_TRANSLATION) {
                    $bIsTextField = false;
                    $aAllowedTypes = array('CMSFIELD_STRING', 'CMSFIELD_TEXT', 'CMSFIELD_STRING_UNIQUE');
                    $oFieldType = $oField->oDefinition->GetFieldType();
                    if (in_array($oFieldType->sqlData['constname'], $aAllowedTypes)) {
                        $bIsTextField = true;
                        $sLastTextFieldName = $oField->name;
                        ++$iTextFieldCount;
                    }

                    if ('1' === $oField->oDefinition->sqlData['is_translatable']) {
                        $sPrefix = TGlobal::GetLanguagePrefix($oTable->GetLanguage());
                        if (empty($sPrefix)) {
                            $sPrefix = $oBaseLanguage->fieldIso6391;
                        }

                        $sTmpFormTabsContent .= '<span class="badge badge-secondary float-right translation-badge mt-1 mr-1">'.$sPrefix."</span>\n";

                        // show icon if record is not translated yet (disabled for MLT fields)
                        if ($oFieldType->fieldBaseType = 'standard' && isset($oTable->sqlData[$oFieldConfig->fieldName.'__'.$sPrefix])) {
                            if ('' == $oTable->sqlData[$oFieldConfig->fieldName.'__'.$sPrefix]) {
                                $sTmpFormTabsContent .= '<span class="badge badge-secondary translationStateIcon float-right mt-1 mr-1 bg-danger">
                                                              <i class="fas fa-language" title="'.TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.cms_module_table_editor.not_translated')).'" title="'.TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.cms_module_table_editor.not_translated')).'"></i>
                                                         </span>';
                            }
                        }
                    }
                }

                $sTmpFormTabsContent .= $oFieldConfig->fieldTranslation;

                if ($oField->IsMandatoryField()) {
                    $sTmpFormTabsContent .= '&nbsp;<span class="requiredfield">*<span>';
                }
                $sTmpFormTabsContent .= '</div>
                <div class="col-sm-10">';
                if ('' !== $oField->oDefinition->sqlData['049_helptext']) {
                    $sTmpFormTabsContent .= '<div class="helptextContainer alert alert-info" id="helptext-'.TGlobal::OutHTML($oField->name).'">'.TGlobal::OutHTML(nl2br($oField->oDefinition->sqlData['049_helptext'])).'</div>';
                }
                $sTmpFormTabsContent .= $oField->GetContent();
                $sTmpFormTabsContent .= '</div></div>';
            }
        }
    }
}

if (!empty($sTmpFormTabsContent)) {
    if (isset($sTabName)) {
        $titleAnchorClass = 'nav-link';
        $titleAriaSelected = 'false';
        $contentClass = 'tab-pane p-0';

        if (1 === $iTabCount) {
            $titleAnchorClass .= ' active';
            $titleAriaSelected = 'true';
            $contentClass .= ' show active';
        }

        $sFormTabsTitles .= '<li class="nav-item"><a id="'.TGlobal::OutHTML(strtolower(TTools::RealNameToURLName($sTabName))).'-tab" class="'.TGlobal::OutHTML($titleAnchorClass).'" data-toggle="tab" href="#tab-'.TGlobal::OutHTML(strtolower(TTools::RealNameToURLName($sTabName))).'" role="tab" aria-controls="tab-'.TGlobal::OutHTML(strtolower(TTools::RealNameToURLName($sTabName))).'" aria-selected="'.TGlobal::OutHTML($titleAriaSelected).'">'.TGlobal::OutHTML($sTabName).'</a></li>';
        $sFormTabsContent .= '<div class="'.$contentClass.'" id="tab-'.strtolower(TTools::RealNameToURLName($sTabName)).'" role="tabpanel" aria-labelledby="'.strtolower(TTools::RealNameToURLName($sTabName)).'">';
    } else {
        $sFormTabsContent .= '<div id="tab-base">';
    }
    $sFormTabsContent .= '<div class="mt-3">';
    $sFormTabsContent .= $sTmpFormTabsContent;
    $sFormTabsContent .= '</div></div>';
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
