<?php
$oNewsletterSignup = $data['oNewsletterSignup'];
/** @var $oNewsletterSignup TdbPkgNewsletterUser */
$oNewsletterConfig = $data['oNewsletterConfig'];  /** @var $oNewsletterConfig TdbPkgNewsletterModuleSignupconfig */
?>

<div class="MTPkgNewsletterSignup">
    <div class="nonewstosignup">
        <div class="headline"><?=TGlobal::OutHTML($oNewsletterConfig->fieldNonewsignupTitle); ?></div>
        <div class="text"><?=$oNewsletterConfig->GetTextField('nonewsignup_text'); ?></div>
    </div>
</div>