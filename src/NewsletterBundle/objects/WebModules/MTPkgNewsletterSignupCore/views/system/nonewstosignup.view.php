<?php
$oNewsletterSignup = $data['oNewsletterSignup'];
/** @var $oNewsletterSignup TdbPkgNewsletterUser */
$oNewsletterConfig = $data['oNewsletterConfig'];  /* @var $oNewsletterConfig TdbPkgNewsletterModuleSignupconfig */
?>

<div class="MTPkgNewsletterSignup">
    <div class="nonewstosignup">
        <div class="headline"><?php echo TGlobal::OutHTML($oNewsletterConfig->fieldNonewsignupTitle); ?></div>
        <div class="text"><?php echo $oNewsletterConfig->GetTextField('nonewsignup_text'); ?></div>
    </div>
</div>