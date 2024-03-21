<?php

/** @var \TdbPkgNewsletterUser $oNewsletterSignup */
/** @var \TdbPkgNewsletterModuleSignupconfig $oNewsletterConfig */
/** @var array $aMainModuleInfo */
/** @var string $sModuleSpotName */
/** @var \TdbPkgNewsletterGroupList $oSignedInNewsletterList */

$oViewRenderer = new ViewRenderer();

$oViewRenderer->addMapperFromIdentifier('chameleon_system_newsletter.mapper.signout_config');
$oViewRenderer->AddSourceObject('oObject', $oNewsletterConfig);
$oViewRenderer->AddSourceObject('sStepName', 'SignOut');

$oViewRenderer->addMapperFromIdentifier('chameleon_system_newsletter.mapper.signout_config_form');
$oViewRenderer->AddSourceObject('oNewsletterUser', $oNewsletterSignup);

$oViewRenderer->AddSourceObject('sModuleSpotName', $sModuleSpotName);
$oViewRenderer->AddSourceObject('oSignedInNewsletterList', $oSignedInNewsletterList);
echo $oViewRenderer->Render('/pkgNewsletter/signOut/signOut.html.twig');
