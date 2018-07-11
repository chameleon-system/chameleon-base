<?php
$oNewsletterSignup = $data['oNewsletterSignup'];
/** @var $oNewsletterSignup TdbPkgNewsletterUser */
$oNewsletterConfig = $data['oNewsletterConfig'];  /** @var $oNewsletterConfig TdbPkgNewsletterModuleSignupconfig */
?>
<div class="MTPkgNewsletterSignup">
    <div class="signedup">
        <div class="headline"><?=TGlobal::OutHTML($oNewsletterConfig->fieldSignedupHeadline); ?></div>
        <div class="text"><?=$oNewsletterConfig->GetTextField('signedup_text'); ?></div>
    </div>
</div>