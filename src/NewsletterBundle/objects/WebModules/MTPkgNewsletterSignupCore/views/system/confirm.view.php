<?php
$oNewsletterSignup = $data['oNewsletterSignup'];
/** @var $oNewsletterSignup TdbPkgNewsletterUser */
$oNewsletterConfig = $data['oNewsletterConfig'];  /* @var $oNewsletterConfig TdbPkgNewsletterModuleSignupconfig */
?>
<div class="MTPkgNewsletterSignup">
    <div class="confirm">
        <div class="headline"><?php echo TGlobal::OutHTML($oNewsletterConfig->fieldConfirmTitle); ?></div>
        <div class="text"><?php echo $oNewsletterConfig->GetTextField('confirm_text'); ?></div>
    </div>
</div>