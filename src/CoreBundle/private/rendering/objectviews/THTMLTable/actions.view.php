<?php
/**
 * @deprecated since 6.3.0 - not used anymore.
 */
/** @var $oHTMLTable THTMLTable */
/** @var $oRecordList TCMSRecordList */
/** @var $oColumns TIterator */

/** @var $sListIdentKey string */
/** @var $iCurrentPage int */
/** @var $sSearchTerm string */

/** @var $aCallTimeVars array */
$sCheckboxFormName = $sListIdentKey.'action';

$sLocation = 'top';
if (array_key_exists('sActionLocation', $aCallTimeVars)) {
    $sLocation = $aCallTimeVars['sActionLocation'];
}
?>
<?php if (count($aActions) > 0) {
    ?>
<tr class="actionrow actionlocation<?=$sLocation; ?>">
    <td class="actionColumn actionRow actionArrow<?=$sLocation; ?>">&nbsp;</td>
    <td class="selectionColumn actionRow" colspan="<?=$oColumns->Length(); ?>">
        <a href="#"
           onclick="THTMLTableSelectAll(document.<?=TGlobal::OutHTML($sCheckboxFormName); ?>,true);return false;"><?=TGlobal::OutHTML(TGlobal::Translate('Alle auswählen')); ?></a>
        | <a href="#"
             onclick="THTMLTableSelectAll(document.<?=TGlobal::OutHTML($sCheckboxFormName); ?>,false);return false;"><?=TGlobal::OutHTML(TGlobal::Translate('Auswahl aufheben')); ?></a>
        <select name="actionselector<?=$sLocation; ?>"
                onchange="if (confirm('<?=TGlobal::OutJS(TGlobal::Translate('Wollen Sie diese Aktion wirklich auf alle ausgewählten Einträge anwenden?')); ?>')) {document.<?=TGlobal::OutHTML($sCheckboxFormName); ?>.elements['module_fnc[<?=TGlobal::OutHTML($sControllingModuleSpotName); ?>]'].value=this.value; document.<?=TGlobal::OutHTML($sCheckboxFormName); ?>.submit();}">
            <option value=""><?=TGlobal::OutHTML(TGlobal::Translate('markierte Einträge:')); ?></option>
            <?php foreach ($aActions as $sMethod => $sName) {
        ?>
            <option value="<?=TGlobal::OutHTML($sMethod); ?>"><?=TGlobal::OutHTML($sName); ?></option>
            <?php
    } ?>
        </select>
    </td>
</tr>
<?php
} ?>
