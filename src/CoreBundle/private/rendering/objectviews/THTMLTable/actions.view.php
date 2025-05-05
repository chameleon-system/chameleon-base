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
<tr class="actionrow actionlocation<?php echo $sLocation; ?>">
    <td class="actionColumn actionRow actionArrow<?php echo $sLocation; ?>">&nbsp;</td>
    <td class="selectionColumn actionRow" colspan="<?php echo $oColumns->Length(); ?>">
        <a href="#"
           onclick="THTMLTableSelectAll(document.<?php echo TGlobal::OutHTML($sCheckboxFormName); ?>,true);return false;"><?php echo TGlobal::OutHTML(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('Alle auswählen')); ?></a>
        | <a href="#"
             onclick="THTMLTableSelectAll(document.<?php echo TGlobal::OutHTML($sCheckboxFormName); ?>,false);return false;"><?php echo TGlobal::OutHTML(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('Auswahl aufheben')); ?></a>
        <select name="actionselector<?php echo $sLocation; ?>"
                onchange="if (confirm('<?php echo TGlobal::OutJS(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('Wollen Sie diese Aktion wirklich auf alle ausgewählten Einträge anwenden?')); ?>')) {document.<?php echo TGlobal::OutHTML($sCheckboxFormName); ?>.elements['module_fnc[<?php echo TGlobal::OutHTML($sControllingModuleSpotName); ?>]'].value=this.value; document.<?php echo TGlobal::OutHTML($sCheckboxFormName); ?>.submit();}">
            <option value=""><?php echo TGlobal::OutHTML(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('markierte Einträge:')); ?></option>
            <?php foreach ($aActions as $sMethod => $sName) {
                ?>
            <option value="<?php echo TGlobal::OutHTML($sMethod); ?>"><?php echo TGlobal::OutHTML($sName); ?></option>
            <?php
            } ?>
        </select>
    </td>
</tr>
<?php
} ?>
