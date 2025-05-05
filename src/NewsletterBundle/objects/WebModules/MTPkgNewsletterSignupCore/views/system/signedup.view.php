<?php
$oNewsletterSignup = $data['oNewsletterSignup'];
/** @var $oNewsletterSignup TdbPkgNewsletterUser */
$oNewsletterConfig = $data['oNewsletterConfig'];  /* @var $oNewsletterConfig TdbPkgNewsletterModuleSignupconfig */
?>
<div class="MTPkgNewsletterSignup">
    <div class="signedup">
        <div class="headline"><?php echo TGlobal::OutHTML($oNewsletterConfig->fieldSignedupHeadline); ?></div>
        <div class="text"><?php echo $oNewsletterConfig->GetTextField('signedup_text'); ?></div>
    </div>
</div>