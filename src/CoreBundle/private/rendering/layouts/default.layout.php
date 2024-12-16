<?php

use ChameleonSystem\CoreBundle\ServiceLocator;
$authenticityTokenManager = ServiceLocator::get('chameleon_system_core.security.authenticity_token.authenticity_token_manager');

$oViewRender = new ViewRenderer();
$oViewRender->AddSourceObject('cmsauthenticitytokenParameter', $authenticityTokenManager->getTokenPlaceholderAsParameter());
echo $oViewRender->Render('BackendLayout/layout.html.twig');

// message garbage collection
// dirty hack to prevent message shown on wrong table editor or table list instance
$oMessageManager = TCMSMessageManager::GetInstance();
$oMessageManager->ClearMessages();
