<?php

/** @var \TdbPkgNewsletterUser $oNewsletterSignup */
/** @var \TdbPkgNewsletterModuleSignupconfig $oNewsletterConfig */
/** @var array $aMainModuleInfo */
$oViewRenderer = new ViewRenderer();

$oViewRenderer->addMapperFromIdentifier('chameleon_system_newsletter.mapper.signup_config');
$oViewRenderer->AddSourceObject('oObject', $oNewsletterConfig);
$oViewRenderer->AddSourceObject('sStepName', 'SignUp');

$oViewRenderer->addMapperFromIdentifier('chameleon_system_newsletter.mapper.signup_config_form');
$oViewRenderer->AddSourceObject('oNewsletterUser', $oNewsletterSignup);
$oViewRenderer->AddSourceObject('sModuleSpotName', $aMainModuleInfo['spotname']);
$oViewRenderer->AddSourceObject('sNewsletterLink', $aMainModuleInfo['URL']);

echo $oViewRenderer->Render('/pkgNewsletter/signUp/signUp.html.twig');
