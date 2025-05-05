<?php

/** @var $oField TCMSField* */
/* @var $bFieldHasErrors boolean* */
/* @var $sForeignTableName string* */
/** @var $oFieldsTargetTable TIterator* */
/* @var $aRecordsConnected array* */
/* @var $aAdditionalFields array* */
/* @var $sConfigMediaFieldName string* */
if (is_array($oField->data) && array_key_exists('x', $oField->data)) {
    unset($oField->data['x']);
}
$sHTML = '';
if (!empty($sForeignTableName) && TTools::FieldExists($sForeignTableName, $oField->sTableName.'_id')) {
    $sConnectedRecordsHTML = '';
    $iCount = 0;
    if (count($aRecordsConnected) > 0) {
        foreach ($aRecordsConnected as $aRow) {
            if (array_key_exists($sConfigMediaFieldName, $aRow) && !empty($aRow[$sConfigMediaFieldName])) {
                $sConnectedRecordsHTML .= '<div id="'.TGlobal::OutHTML($oField->name).$iCount.'" class="'.TGlobal::OutHTML($oField->name).'container existing">';
                // deactivates runtime caching temporary, because the path has not be set in runtime caching
                // as this image was saved TCMSFieldPropertyTable_CmsMedia::UploadImage =>TCMSTableEditor::Save() =>TCMSTableEditor::_WriteDataToDatabase()
                TCacheManagerRuntimeCache::SetEnableAutoCaching(false);
                $oImage = new TCMSImage();
                $oImage->Load($aRow[$sConfigMediaFieldName]);
                TCacheManagerRuntimeCache::SetEnableAutoCaching(true);
                $oThumb = $oImage->GetThumbnail(100, 100);
                $sConnectedRecordsHTML .= '
            <img src="'.$oThumb->GetFullURL().'" alt="'.TGlobal::OutHTML($oImage->aData['description']).'" />
          ';
                if (is_array($aAdditionalFields) && count($aAdditionalFields) > 0) {
                    foreach ($aAdditionalFields as $sFieldName) {
                        $oFieldTargetTable = TdbCmsFieldConf::GetNewInstance();
                        if ($oFieldTargetTable->LoadFromFields(['name' => $sFieldName, 'cms_tbl_conf_id' => TTools::GetCMSTableId($sForeignTableName)])) {
                            $sValue = '';
                            if (array_key_exists($sFieldName, $aRow)) {
                                $sValue = $aRow[$sFieldName];
                            }
                            $sConnectedRecordsHTML .= '
                  <label for="'.TGlobal::OutHTML($oField->name).'['.TGlobal::OutHTML($iCount).']['.TGlobal::OutHTML($sFieldName).']">'.TGlobal::OutHTML($oFieldTargetTable->fieldTranslation).'</label>
                  <input type="text" name="'.TGlobal::OutHTML($oField->name).'['.TGlobal::OutHTML($iCount).']['.TGlobal::OutHTML($sFieldName).']" value="'.TGlobal::OutHTML($sValue).'" />
                ';
                        }
                    }
                }

                if (array_key_exists('id', $aRow)) {
                    $_SESSION['pkgFormUploadedImagesByUser'][] = $aRow[$sConfigMediaFieldName];
                    $sConnectedRecordsHTML .= '<input type="hidden" name="'.TGlobal::OutHTML($oField->name).'['.TGlobal::OutHTML($iCount).'][id]" value="'.TGlobal::OutHTML($aRow['id']).'" />';
                }

                $sConnectedRecordsHTML .= '<input type="hidden" name="'.TGlobal::OutHTML($oField->name).'['.TGlobal::OutHTML($iCount).'][presaveimage]" value="'.TGlobal::OutHTML($aRow[$sConfigMediaFieldName]).'" />';

                $sConnectedRecordsHTML .= '<div class="remove '.TGlobal::OutHTML($oField->name).'remove"><span class="removebutton" onclick="$(this).parent().parent().remove();return false;">x</span></div></div>';
                ++$iCount;
            }
        }
    }
    $sHTML .= '
      <script type="text/javascript">
        top.Counter'.TGlobal::OutJS($sForeignTableName).' = '.$iCount.';
        function AddNew'.TGlobal::OutJS($sForeignTableName).'() {
          sAppendHTML = "<div class=\"'.TGlobal::OutJS($oField->name).'container new\" id=\"'.TGlobal::OutJS($oField->name).'"+top.Counter'.TGlobal::OutJS($sForeignTableName).'+"\">";
    ';

    $sHTML .= '
      sAppendHTML += "<input type=\"file\" size=\"30\" name=\"'.TGlobal::OutJS($oField->name).'image["+top.Counter'.TGlobal::OutJS($sForeignTableName).'+"]\" \/>";
    ';

    if (is_array($aAdditionalFields) && count($aAdditionalFields) > 0) {
        foreach ($aAdditionalFields as $sFieldName) {
            $oFieldTargetTable = TdbCmsFieldConf::GetNewInstance();
            if ($oFieldTargetTable->LoadFromFields(['name' => $sFieldName, 'cms_tbl_conf_id' => TTools::GetCMSTableId($sForeignTableName)])) {
                $sHTML .= '
            sAppendHTML += "<label for=\"'.TGlobal::OutJS($oField->name).'["+top.Counter'.TGlobal::OutJS($sForeignTableName).'+"]['.TGlobal::OutJS($sFieldName).']\">'.TGlobal::OutJS($oFieldTargetTable->fieldTranslation).'<\/label>";
            sAppendHTML += "<input type=\"text\" name=\"'.TGlobal::OutJS($oField->name).'["+top.Counter'.TGlobal::OutJS($sForeignTableName).'+"]['.TGlobal::OutJS($sFieldName).']\" \/>";
          ';
            }
        }
    }

    $sHTML .= '
      sAppendHTML += "<input type=\"hidden\" name=\"'.TGlobal::OutJS($oField->name).'["+top.Counter'.TGlobal::OutJS($sForeignTableName).'+"]['.TGlobal::OutJS($sConfigMediaFieldName).']\" \/>";
    ';

    $sHTML .= '
          sAppendHTML += "<div class=\"remove '.TGlobal::OutJS($oField->name).'remove\"><span class=\"removebutton\" onclick=\"$(this).parent().parent().remove();return false;\">'.TGlobal::OutHTML(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.field_property_media.remove')).'<\/span><\/div>";
          $("#'.TGlobal::OutJS($oField->name).'").append(sAppendHTML);
          top.Counter'.TGlobal::OutJS($sForeignTableName).'++;
        }
      </script>
      <div id="'.TGlobal::OutHTML($oField->name).'">
        <input type="hidden" name="'.TGlobal::OutHTML($oField->name).'[x]" value="x" />
        <div class="add '.TGlobal::OutHTML($oField->name).'add"><span class="addbutton" onclick="AddNew'.TGlobal::OutHTML($sForeignTableName).'();return false;">'.TGlobal::OutHTML(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.field_property_media.add_new_media')).'</span></div>
        '.$sConnectedRecordsHTML.'
      </div>
    ';
}
echo $sHTML;
