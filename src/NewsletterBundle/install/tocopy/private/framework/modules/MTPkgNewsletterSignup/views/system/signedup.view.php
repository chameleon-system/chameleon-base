<?php

/** @var $oNewsletterSignup TdbPkgNewsletterUser */
/** @var $oNewsletterConfig TdbPkgNewsletterModuleSignupconfig */
$oViewRenderer = new ViewRenderer();

$oViewRenderer->addMapperFromIdentifier('chameleon_system_newsletter.mapper.signup_config');
$oViewRenderer->AddSourceObject('oObject', $oNewsletterConfig);
$oViewRenderer->AddSourceObject('sStepName', 'SignedUp');

echo $oViewRenderer->Render('/pkgNewsletter/signUp/signedUp.html.twig');
