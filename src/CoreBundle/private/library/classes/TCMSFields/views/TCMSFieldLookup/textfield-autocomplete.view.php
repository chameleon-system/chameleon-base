<?php
/** @var $oField TCMSField* */
/** @var $bFieldHasErrors boolean* */
/** @var $aOptions array* */
$oActivePage = \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.active_page_service')->getActivePage();
$oGlobal = TGlobal::instance();
$aAutocompleteParams = array('module_fnc' => array($oGlobal->GetExecutingModulePointer()->sModuleSpotName => 'ExecuteAjaxCall'), '_fnc' => 'GetAutoCompleteList', 'sTableName' => $oField->GetConnectedTableName(), 'sOutputMode' => 'Plain');
$sAutoCompleteURL = $oActivePage->GetRealURLPlain($aAutocompleteParams, true);
$sValue = '';
$sId = '';
foreach ($aOptions as $key => $value) {
    if (0 == strcmp($oField->data, $key)) {
        $sValue = TGlobal::OutHTML($value);
        $sId = $key;
    }
}
echo '<input type="text" name="'.TGlobal::OutHTML($oField->name).'_autocompleter" id="'.TGlobal::OutHTML($oField->name).'_autocompleter" value="'.$sValue.'">';
echo '<input type="hidden" name="'.TGlobal::OutHTML($oField->name).'" id="'.TGlobal::OutHTML($oField->name).'" value="'.TGlobal::OutHTML($sId).'">';
?>
<script type="text/javascript">
    $(document).ready(function () {
        $('#<?=TGlobal::OutHTML($oField->name); ?>_autocompleter').autocomplete("<?=str_replace('&amp;', '&', urldecode($sAutoCompleteURL)); ?>", {'mustMatch':true}).result(function (event, item) {
            if (typeof(item) == 'undefined' || typeof(item[1]) == 'undefined') $('#<?=TGlobal::OutHTML($oField->name); ?>').val(''); else {
                $('#<?=TGlobal::OutHTML($oField->name); ?>').val(item[1]);
            }
        });
    });
</script>