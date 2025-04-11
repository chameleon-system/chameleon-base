<?php
/** @var TdbCmsTblFieldTab $oTab */
use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\CoreBundle\Util\UrlNormalization\UrlNormalizationUtil;

$markdownParserService = ServiceLocator::get('chameleon_system_markdown_cms.markdown_parser_service');

$data['oFields']->GoToStart();
$sTmpFormTabsContent = '';
while ($oField = $data['oFields']->Next()) {
    /** @var $oField TCMSField */
    if ($sTabId == $oField->oDefinition->sqlData['cms_tbl_field_tab']) {
        if ('hidden' != $oField->GetDisplayType()) {
            $headlineColorStyle = '';
            if (!empty($oField->oDefinition->sqlData['row_hexcolor'])) {
                $headlineColorStyle = ' style="color: #'.TGlobal::OutHTML($oField->oDefinition->sqlData['row_hexcolor']).'; font-weight: bold; border-color: #'.TGlobal::OutHTML($oField->oDefinition->sqlData['row_hexcolor']).' !important;"';
            }

            ++$rowCount;
            if ($oField->completeRow) { // Headline row
                $sTmpFormTabsContent .= '<div class="row border-bottom table-editor-form-group mb-2"'.$headlineColorStyle.'>
                <div class="col-sm-12">
                    <div class="d-flex">
                        <h4'.$headlineColorStyle.'>';
                $sTmpFormTabsContent .= $oField->GetContent();
                $sTmpFormTabsContent .= '</h4>';
                if ('' !== $oField->oDefinition->sqlData['049_helptext']) {
                    $sTmpFormTabsContent .= '<span class="help-text-button pl-2" data-helptextId="'.TGlobal::OutHTML($oField->name).'">
                        <i class="fas fa-info-circle" title="'.TGlobal::OutHTML(\ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.cms_module_table_editor.field_help')).'"></i>
                    </span>';
                }
                $sTmpFormTabsContent .= '
                            </div>
                        </div>
                    </div>';

                if ('' !== $oField->oDefinition->sqlData['049_helptext']) {
                    $sTmpFormTabsContent .= '<div class="helptextContainer alert alert-info" id="helptext-'.TGlobal::OutHTML($oField->name).'">
                    '.
                        $markdownParserService->getMarkdownParser()->convert($oField->oDefinition->sqlData['049_helptext']).
                        '</div>';
                }
            } else {
                if (true === $isReadOnly) { // overwrite field type with readonly (e.g. record is locked by another cms user)
                    $oField->oDefinition->sqlData['modifier'] = 'readonly';
                }

                $sTmpFormTabsContent .= '
                <div class="form-group table-editor-form-group row" id="fieldname_'.TGlobal::OutHTML($oField->name).'">
                    <div class="col-xl-3 col-sm-4 col-form-label d-flex justify-content-between align-items-start">
            ';

                $oFieldConfig = TdbCmsFieldConf::GetNewInstance();
                $oFieldConfig->Load($oField->oDefinition->id);

                $sTmpFormTabsContent .= '<div>';
                $sTmpFormTabsContent .= '<span'.$headlineColorStyle.'>'.$oFieldConfig->fieldTranslation.'</span>';

                if ($oField->IsMandatoryField()) {
                    $sTmpFormTabsContent .= '&nbsp;<span class="requiredfield">*<span>';
                }
                $sTmpFormTabsContent .= '</div>';

                $sTmpFormTabsContent .= '<div class="d-flex">';
                if (ACTIVE_TRANSLATION) {
                    $oFieldType = $oField->oDefinition->GetFieldType();
                    if ('1' === $oField->oDefinition->sqlData['is_translatable']) {
                        $sPrefix = TGlobal::GetLanguagePrefix($oTable->GetLanguage());
                        if (empty($sPrefix)) {
                            $sPrefix = $oBaseLanguage->fieldIso6391;
                        }

                        $sTmpFormTabsContent .= '<span class="badge badge-secondary translation-badge mr-1">'.$sPrefix."</span>\n";

                        // show icon if record is not translated yet (disabled for MLT fields)
                        if ($oFieldType->fieldBaseType = 'standard' && isset($oTable->sqlData[$oFieldConfig->fieldName.'__'.$sPrefix])) {
                            if ('' == $oTable->sqlData[$oFieldConfig->fieldName.'__'.$sPrefix]) {
                                $sTmpFormTabsContent .= '<span class="badge badge-secondary translationStateIcon mr-1 bg-danger">
                                                              <i class="fas fa-language" title="'.TGlobal::OutHTML(\ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.cms_module_table_editor.not_translated')).'" title="'.TGlobal::OutHTML(\ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.cms_module_table_editor.not_translated')).'"></i>
                                                         </span>';
                            }
                        }
                    }
                }

                if (!empty($oField->oDefinition->sqlData['049_helptext'])) {
                    $sTmpFormTabsContent .= '<span class="help-text-button" data-helptextId="'.TGlobal::OutHTML($oField->name).'">
                        <i class="fas fa-info-circle" title="'.TGlobal::OutHTML(\ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.cms_module_table_editor.field_help')).'"></i>
                    </span>';
                }

                $sTmpFormTabsContent .= '</div>';

                $sTmpFormTabsContent .= '</div>';
                $sTmpFormTabsContent .= '<div class="col-xl-9 col-sm-8 col-form-content">';
                if ('' !== $oField->oDefinition->sqlData['049_helptext']) {
                    $sTmpFormTabsContent .= '<div class="helptextContainer alert alert-info" id="helptext-'.TGlobal::OutHTML($oField->name).'">'.
                        $markdownParserService->getMarkdownParser()->convert($oField->oDefinition->sqlData['049_helptext']).
                        '</div>';
                }
                $sTmpFormTabsContent .= $oField->GetContent();
                $sTmpFormTabsContent .= $oField->getFieldExtensionHtml();
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

        if (isset($firstActiveTab) && true === $firstActiveTab) {
            $titleAnchorClass .= ' active';
            $titleAriaSelected = 'true';
            $contentClass .= ' show active';
        }

        /**
         * @var UrlNormalizationUtil $urlNormalizationUtil
         */
        $urlNormalizationUtil = ServiceLocator::get('chameleon_system_core.util.url_normalization');
        $urlTabName = \strtolower($urlNormalizationUtil->normalizeUrl($sTabName));

        $sFormTabsTitles .= '<li class="nav-item"><a id="'.TGlobal::OutHTML($urlTabName).'-tab" class="'.TGlobal::OutHTML($titleAnchorClass).'" data-toggle="tab" href="#tab-'.TGlobal::OutHTML($urlTabName).'" role="tab" aria-controls="tab-'.TGlobal::OutHTML($urlTabName).'" aria-selected="'.TGlobal::OutHTML($titleAriaSelected).'">'.TGlobal::OutHTML($sTabName).'</a></li>';
        $sFormTabsContent .= '<div class="'.$contentClass.'" id="tab-'.$urlTabName.'" role="tabpanel" aria-labelledby="'.$urlTabName.'">';
    } else {
        $sFormTabsContent .= '<div id="tab-base">';
    }
    $sFormTabsContent .= '<div class="mt-3">';
    $sFormTabsContent .= $sTmpFormTabsContent;
    $sFormTabsContent .= '</div></div>';
}
