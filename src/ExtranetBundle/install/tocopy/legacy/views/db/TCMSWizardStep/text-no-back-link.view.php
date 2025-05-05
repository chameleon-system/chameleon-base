<?php
/* @var $oStep TdbCmsWizardStep */
$oUser = TdbDataExtranetUser::GetInstance();
$oExtranetConfig = TdbDataExtranet::GetInstance();

?>
<div class="step">
  <?php
    if (!empty($oStep->fieldName)) {
        echo '<h2 class="largeHeadline">'.TGlobal::OutHTML($oStep->fieldName).'</h2><br />';
    }
echo $oStep->GetTextField('description');
?>
  <form name="checkout" accept-charset="utf-8" method="post" action="">
    <input type="hidden" name="module_fnc[<?php echo TGlobal::OutHTML($sSpotName); ?>]" value="ExecuteStep" />
    <input type="hidden" name="<?php echo TGlobal::OutHTML(MTCMSWizardCore::URL_PARAM_STEP_METHOD); ?>" value="" />

    <div class="stepnavibuttons">
      <?php if (!is_null($oStepNext)) {
          ?><div class="formButtonNext"><input type="submit" value="<?php echo TGlobal::OutHTML(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_extranet.action.next_step')); ?>" /></div><?php
      } ?>
    </div>
  </form>

</div>