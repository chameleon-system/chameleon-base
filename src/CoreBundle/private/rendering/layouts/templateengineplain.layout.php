<?php

use ChameleonSystem\CoreBundle\ServiceLocator;
$authenticityTokenManager = ServiceLocator::get('chameleon_system_core.security.authenticity_token.authenticity_token_manager');

$viewRender = new ViewRenderer();
$viewRender->AddSourceObject('cmsauthenticitytokenParameter', $authenticityTokenManager->getTokenPlaceholderAsParameter());
echo $viewRender->Render('BackendLayout/templateengine-plain.html.twig');

