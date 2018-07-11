<?php
$oNewsletterSignup = $data['oNewsletterSignup'];
/** @var $oNewsletterSignup TdbPkgNewsletterUser */
$oNewsletterConfig = $data['oNewsletterConfig'];  /** @var $oNewsletterConfig TdbPkgNewsletterModuleSignupconfig */
?>
<div class="MTPkgNewsletterSignup">
    <div class="confirm">
        <div class="headline"><?=TGlobal::OutHTML($oNewsletterConfig->fieldConfirmTitle); ?></div>
        <div class="text"><?=$oNewsletterConfig->GetTextField('confirm_text'); ?></div>
    </div>
</div>