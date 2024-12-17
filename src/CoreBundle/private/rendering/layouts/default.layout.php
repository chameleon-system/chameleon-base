<?php

use ChameleonSystem\CoreBundle\ServiceLocator;

$authenticityTokenManager = ServiceLocator::get('chameleon_system_core.security.authenticity_token.authenticity_token_manager');

$viewRender = new ViewRenderer();
$viewRender->AddSourceObject('cmsauthenticitytokenParameter', $authenticityTokenManager->getTokenPlaceholderAsParameter());
echo $viewRender->Render('BackendLayout/default.html.twig');

// message garbage collection
// dirty hack to prevent message shown on wrong table editor or table list instance
$flashMessage = ServiceLocator::get('chameleon_system_core.flash_messages');
$flashMessage->ClearMessages();
