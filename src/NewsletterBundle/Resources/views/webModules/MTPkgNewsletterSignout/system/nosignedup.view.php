<?php

/** @var \TdbPkgNewsletterModuleSignupconfig $oNewsletterConfig */
$oViewRenderer = new ViewRenderer();

$oViewRenderer->addMapperFromIdentifier('chameleon_system_newsletter.mapper.signout_config');
$oViewRenderer->AddSourceObject('oObject', $oNewsletterConfig);
$oViewRenderer->AddSourceObject('sStepName', 'NoNewsToSignOut');

echo $oViewRenderer->Render('/pkgNewsletter/signOut/noNewsToSignOut.html.twig');
