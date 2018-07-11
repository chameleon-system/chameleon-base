<?php
/** @var $oHTMLTable THTMLTable */
/** @var $oRecordList TCMSRecordList */
/** @var $oColumns TIterator */

/** @var $sListIdentKey string */
/** @var $iCurrentPage int */
/** @var $sSearchTerm string */

/** @var $aCallTimeVars array */
$bShowActionCheckbox = (count($aActions) > 0);
?>
<div class="THTMLTable">
    <div class="standard">
        <?php if ($oHTMLTable->bShowGlobalSearchForm) {
    echo $oHTMLTable->RenderGlobalSearchForm($aCallTimeVars);
} ?>

        <?php if ($bShowActionCheckbox && !$oHTMLTable->bShowColumnSearchFields) { // if we do not have search fields we can wrap the checkbox form around the table (proper html)?>
    <form name="<?=TGlobal::OutHTML($sListIdentKey); ?>action" accept-charset="utf-8" action="<?=$oHTMLTable->GetURL(); ?>"
          method="post">
    <input type="hidden" name="module_fnc[<?=TGlobal::OutHTML($sControllingModuleSpotName); ?>]" value=""/>
        <?php
} ?>
        <table summary=""
               class="listtable <?=TGlobal::OutHTML($sListIdentKey); ?> <?=TGlobal::OutHTML(get_class($oRecordList)); ?>">
            <?=$oHTMLTable->Render('header', 'Core', $aCallTimeVars); ?>
            <?php if ($oHTMLTable->bShowColumnSearchFields) {
    echo $oHTMLTable->Render('filter', 'Core', $aCallTimeVars);
} ?>

            <?php
            if ($bShowActionCheckbox) {
                // open the checkobx form here if we had search fields (search fields contain form elements, so we can not wrap the checkbox form around them
                if ($oHTMLTable->bShowColumnSearchFields) {
                    ?>
          <form name="<?=TGlobal::OutHTML($sListIdentKey); ?>action" accept-charset="utf-8"
                action="<?=$oHTMLTable->GetURL(); ?>" method="post">
          <input type="hidden" name="module_fnc[<?=TGlobal::OutHTML($sControllingModuleSpotName); ?>]" value=""/>
                    <?php
                }
                $aActionCallVars = $aCallTimeVars;
                $aActionCallVars['sActionLocation'] = 'top';
                echo $oHTMLTable->Render('actions', 'Core', $aActionCallVars);
            }
            ?>
            <?=$oHTMLTable->Render('rows', 'Core', $aCallTimeVars); ?>
            <?php
            if ($bShowActionCheckbox) {
                $aActionCallVars = $aCallTimeVars;
                $aActionCallVars['sActionLocation'] = 'bottom';
                echo $oHTMLTable->Render('actions', 'Core', $aActionCallVars);

                // close the checkbox form if it had to be opend within the table because of existing search fields
                if ($oHTMLTable->bShowColumnSearchFields) {
                    ?></form><?php
                }
            }
            ?>

        </table>
        <?php if ($bShowActionCheckbox && !$oHTMLTable->bShowColumnSearchFields) { // close the checkobx form here  if we do not have search fields (in which case we can wrap the checkbox form around the table (proper html))?>
    </form>
  <?php
            } ?>

        <?=$oHTMLTable->Render('tablenavi', 'Core', $aCallTimeVars); ?>
    </div>
</div>