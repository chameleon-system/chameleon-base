<?php

/** @var $oField TCMSField* */
/* @var $bFieldHasErrors boolean* */
/* @var $aRecordsConnected array* */
/* @var $sForeignTableName string* */
/* @var $aAdditionalFields array* */
/* @var $aRecordsConnected array* */
if (is_array($oField->data) && array_key_exists('x', $oField->data)) {
    unset($oField->data['x']);
}
$sHTML = '';
if (!empty($sForeignTableName)) {
    $sConnectedRecordsHTML = '';
    $iCount = 0;
    if (count($aRecordsConnected) > 0) {
        foreach ($aRecordsConnected as $aRow) {
            if (array_key_exists('id', $aRow) && !empty($aRow['id'])) {
                $sConnectedRecordsHTML .= '<div id="'.TGlobal::OutHTML($oField->name).$iCount.'" class="'.TGlobal::OutHTML($oField->name).'container existing">';

                $oDocument = TdbCmsDocument::GetNewInstance();
                $oDocument->Load($aRow['id']);

                $sConnectedRecordsHTML .= $oDocument->getDownloadHtmlTag();

                if (is_array($aAdditionalFields) && count($aAdditionalFields) > 0) {
                    foreach ($aAdditionalFields as $sFieldName) {
                        $oAdditionalField = TdbCmsFieldConf::GetNewInstance();
                        if ($oAdditionalField->LoadFromFields(['name' => $sFieldName, 'cms_tbl_conf_id' => TTools::GetCMSTableId($sForeignTableName)])) {
                            $sValue = '';
                            if (array_key_exists($sFieldName, $aRow)) {
                                $sValue = $aRow[$sFieldName];
                            }
                            $sConnectedRecordsHTML .= '
                  <label for="'.TGlobal::OutHTML($oField->name).'['.TGlobal::OutHTML($iCount).']['.TGlobal::OutHTML($sFieldName).']">'.TGlobal::OutHTML($oAdditionalField->fieldTranslation).'</label>
                  <input type="text" name="'.TGlobal::OutHTML($oField->name).'['.TGlobal::OutHTML($iCount).']['.TGlobal::OutHTML($sFieldName).']" value="'.TGlobal::OutHTML($sValue).'" />
                ';
                        }
                    }
                }

                if (array_key_exists('id', $aRow)) {
                    $_SESSION['pkgFormUploadedDocuemntsByUser'][] = $aRow['id'];
                    $sConnectedRecordsHTML .= '<input type="hidden" name="'.TGlobal::OutHTML($oField->name).'['.TGlobal::OutHTML($iCount).'][id]" value="'.TGlobal::OutHTML($aRow['id']).'" />';
                }

                $sConnectedRecordsHTML .= '<input type="hidden" name="'.TGlobal::OutHTML($oField->name).'['.TGlobal::OutHTML($iCount).'][presavedocument]" value="'.TGlobal::OutHTML($aRow['id']).'" />';

                $sConnectedRecordsHTML .= '<div class="remove '.TGlobal::OutHTML($oField->name).'remove"><span class="removebutton" onclick="$(this).parent().parent().remove();return false;">x</span></div></div>';
                ++$iCount;
            }
        }
    }
    $sHTML .= '
      <script type="text/javascript">
        top.Counter'.TGlobal::OutJS($sForeignTableName.$oField->name).' = '.$iCount.';
        function AddNew'.TGlobal::OutJS($sForeignTableName.$oField->name).'() {
          sAppendHTML = "<div class=\"'.TGlobal::OutJS($oField->name).'container new\" id=\"'.TGlobal::OutJS($oField->name).'"+top.Counter'.TGlobal::OutJS($sForeignTableName.$oField->name).'+"\">";
    ';

    $sHTML .= '
      sAppendHTML += "<input type=\"file\" size=\"30\" name=\"'.TGlobal::OutJS($oField->name).'document["+top.Counter'.TGlobal::OutJS($sForeignTableName.$oField->name).'+"]\" \/>";
    ';

    if (is_array($aAdditionalFields) && count($aAdditionalFields) > 0) {
        foreach ($aAdditionalFields as $sFieldName) {
            $oAdditionalField = TdbCmsFieldConf::GetNewInstance();
            if ($oAdditionalField->LoadFromFields(['name' => $sFieldName, 'cms_tbl_conf_id' => TTools::GetCMSTableId($sForeignTableName)])) {
                $sHTML .= '
            sAppendHTML += "<label for=\"'.TGlobal::OutJS($oField->name).'["+top.Counter'.TGlobal::OutJS($sForeignTableName.$oField->name).'+"]['.TGlobal::OutJS($sFieldName).']\">'.TGlobal::OutJS($oAdditionalField->fieldTranslation).'<\/label>";
            sAppendHTML += "<input type=\"text\" name=\"'.TGlobal::OutJS($oField->name).'["+top.Counter'.TGlobal::OutJS($sForeignTableName.$oField->name).'+"]['.TGlobal::OutJS($sFieldName).']\" \/>";
          ';
            }
        }
    }

    $sHTML .= '
      sAppendHTML += "<input type=\"hidden\" name=\"'.TGlobal::OutJS($oField->name).'["+top.Counter'.TGlobal::OutJS($sForeignTableName.$oField->name).'+"][id]\" \/>";
    ';

    $sHTML .= '
          sAppendHTML += "<div class=\"remove '.TGlobal::OutJS($oField->name).'remove\"><span class=\"removebutton\" onclick=\"$(this).parent().parent().remove();return false;\">'.TGlobal::OutHTML(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.field_download.remove')).'<\/span><\/div>";
          $("#'.TGlobal::OutJS($oField->name).'").append(sAppendHTML);
          top.Counter'.TGlobal::OutJS($sForeignTableName.$oField->name).'++;
        }
      </script>
      <div id="'.TGlobal::OutHTML($oField->name).'">
        <input type="hidden" name="'.TGlobal::OutHTML($oField->name).'[x]" value="x" />
        <div class="add '.TGlobal::OutHTML($oField->name).'add"><span class="addbutton" onclick="AddNew'.TGlobal::OutHTML($sForeignTableName.$oField->name).'();return false;">'.TGlobal::OutHTML(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.field_download.add_new')).'</span></div>
        '.$sConnectedRecordsHTML.'
      </div>
    ';
}
echo $sHTML;
