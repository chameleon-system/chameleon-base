<?php

/** @var $oNewsletterSignup TdbPkgNewsletterUser */
/** @var $oNewsletterConfig TdbPkgNewsletterModuleSignupconfig */
/** @var $aMainModuleInfo array */
$oViewRenderer = new ViewRenderer();

$oViewRenderer->addMapperFromIdentifier('chameleon_system_newsletter.mapper.signout_config');
$oViewRenderer->AddSourceObject('oObject', $oNewsletterConfig);
$oViewRenderer->AddSourceObject('sStepName', 'SignOut');

$oViewRenderer->addMapperFromIdentifier('chameleon_system_newsletter.mapper.signout_config_form');
$oViewRenderer->AddSourceObject('oNewsletterUser', $oNewsletterSignup);
$oViewRenderer->AddSourceObject('sModuleSpotName', $sModuleSpotName);
$oViewRenderer->AddSourceObject('oSignedInNewsletterList', $oSignedInNewsletterList);
echo $oViewRenderer->Render('/pkgNewsletter/signOut/signOut.html.twig');
