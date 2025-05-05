<?php

/** @var $oField TCMSField* */
/** @var $bFieldHasErrors boolean* */
/** @var $sForeignTableName string* */
/** @var $oFieldsTargetTable TIterator* */
/** @var $aRecordsConnected array* */
$sHTML = '';
if (is_array($oField->data) && array_key_exists('x', $oField->data)) {
    unset($oField->data['x']);
}
if (!empty($sForeignTableName) && TTools::FieldExists($sForeignTableName, $oField->sTableName.'_id')) {
    $sConnectedRecordsHTML = '';
    $iCount = 0;
    if (count($aRecordsConnected) > 0) {
        foreach ($aRecordsConnected as $aRow) {
            $sConnectedRecordsHTML .= '<div id="'.TGlobal::OutHTML($oField->name).$iCount.'" class="'.TGlobal::OutHTML($oField->name).'container">';
            while ($oFieldTargetTable = $oFieldsTargetTable->Next()) {
                if ($oFieldTargetTable->fieldName != $oField->sTableName.'_id' && array_key_exists($oFieldTargetTable->fieldName, $aRow)) {
                    $sConnectedRecordsHTML .= '<div class="fieldcontainer '.TGlobal::OutHTML($oField->name).TGlobal::OutHTML($oFieldTargetTable->fieldName).'fieldcontainer"><div class="legend '.TGlobal::OutHTML($oField->name).'legend '.TGlobal::OutHTML($oField->name).TGlobal::OutHTML($oFieldTargetTable->fieldName).'legend">'.$oField->GetFieldNameForFieldFrontend($oFieldTargetTable).'</div><div class="input '.TGlobal::OutHTML($oField->name).'input '.TGlobal::OutHTML($oField->name).TGlobal::OutHTML($oFieldTargetTable->fieldName).'input"><input type="text" name="'.TGlobal::OutHTML($oField->name).'['.TGlobal::OutHTML($iCount).']['.TGlobal::OutHTML($oFieldTargetTable->fieldName).']" value="'.TGlobal::OutHTML($aRow[$oFieldTargetTable->fieldName]).'" /></div></div>
              ';
                }
            }
            if (array_key_exists('id', $aRow)) {
                $sConnectedRecordsHTML .= '<input type="hidden" name="'.TGlobal::OutHTML($oField->name).'['.TGlobal::OutHTML($iCount).'][id]" value="'.TGlobal::OutHTML($aRow['id']).'"';
            }
            $sConnectedRecordsHTML .= '<div class="remove '.TGlobal::OutHTML($oField->name).'remove"><span class="removebutton" onclick="$(this).parent().parent().remove();return false;">'.TGlobal::OutHTML(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.field_property.remove')).'</span></div></div>';
            $oFieldsTargetTable->GoToStart();
            ++$iCount;
        }
    }
    $sHTML .= '
        <script type="text/javascript">
          top.Counter'.TGlobal::OutJS($sForeignTableName).' = '.$iCount.';
          function AddNew'.TGlobal::OutJS($sForeignTableName).'() {
            sAppendHTML = "<div class=\"'.TGlobal::OutJS($oField->name).'container\" id=\"'.TGlobal::OutJS($oField->name).'"+top.Counter'.TGlobal::OutJS($sForeignTableName).'+"\">";
      ';
    $sJSCalls = '';
    while ($oFieldTargetTable = $oFieldsTargetTable->Next()) {
        if ($oFieldTargetTable->fieldName != $oField->sTableName.'_id') {
            $sJSCalls .= $oCmsField->getFrontendJavascriptInitMethodOnSubRecordLoad();
            $sHTML .= '
            sAppendHTML += "<div class=\"fieldcontainer '.TGlobal::OutHTML($oField->name).TGlobal::OutJS($oFieldTargetTable->fieldName).'fieldcontainer\"><div class=\"legend '.TGlobal::OutJS($oField->name).'legend '.TGlobal::OutJS($oField->name).TGlobal::OutJS($oFieldTargetTable->fieldName).'legend\">'.$oField->GetFieldNameForFieldFrontend($oFieldTargetTable).'<\/div><div class=\"input '.TGlobal::OutJS($oField->name).'input '.TGlobal::OutJS($oField->name).TGlobal::OutJS($oFieldTargetTable->fieldName).'input\"><input type=\"text\" name=\"'.TGlobal::OutJS($oField->name).'["+top.Counter'.TGlobal::OutJS($sForeignTableName).'+"]['.TGlobal::OutJS($oFieldTargetTable->fieldName).']\" \/><\/div><\/div>";
          ';
        }
    }
    $sHTML .= '
            sAppendHTML += "<div class=\"remove '.TGlobal::OutJS($oField->name).'remove\"><span class=\"removebutton\" onclick=\"$(this).parent().parent().remove();return false;\">'.TGlobal::OutHTML(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.field_property.remove')).'<\/span><\/div>";
            $("#'.TGlobal::OutJS($oField->name).'").append(sAppendHTML);
            top.Counter'.TGlobal::OutJS($sForeignTableName).'++;
            '.$sJSCalls.'
          }
        </script>
        <div id="'.TGlobal::OutHTML($oField->name).'">
          <input type="hidden" name="'.TGlobal::OutHTML($oField->name).'[x]" value="x" />
          <div class="add '.TGlobal::OutHTML($oField->name).'add"><span class="addbutton" onclick="AddNew'.TGlobal::OutHTML($sForeignTableName).'();return false;">'.TGlobal::OutHTML(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.field_property.add_new')).'</span></div>
          '.$sConnectedRecordsHTML.'
        </div>
      ';
}
echo $sHTML;
