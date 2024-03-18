<?php

/** @var $oNewsletterSignup TdbPkgNewsletterUser */
/** @var $oNewsletterConfig TdbPkgNewsletterModuleSignupconfig */
/** @var $aMainModuleInfo array */
$oViewRenderer = new ViewRenderer();

$oViewRenderer->addMapperFromIdentifier('chameleon_system_newsletter.mapper.signup_config');
$oViewRenderer->AddSourceObject('oObject', $oNewsletterConfig);
$oViewRenderer->AddSourceObject('sStepName', 'SignUp');

$oViewRenderer->addMapperFromIdentifier('chameleon_system_newsletter.mapper.signup_config_form');
$oViewRenderer->AddSourceObject('oNewsletterUser', $oNewsletterSignup);
$oViewRenderer->AddSourceObject('sModuleSpotName', $aMainModuleInfo['spotname']);
$oViewRenderer->AddSourceObject('sNewsletterLink', $aMainModuleInfo['URL']);

echo $oViewRenderer->Render('/pkgNewsletter/signUp/signUp.html.twig');
