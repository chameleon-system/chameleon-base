<?php

$oUser = TdbDataExtranetUser::GetNewInstance();
$oViewRender = new ViewRenderer();
$oViewRender->AddMapper(new TCMSWizardStepMapper_UserProfile());
$oViewRender->AddSourceObject('sSpotName', $sSpotName);
$oViewRender->AddSourceObject('oObject', $oStep);
$oViewRender->AddSourceObject('sWizardModuleModuleSpot', MTCMSWizardCore::URL_PARAM_MODULE_SPOT);
$oViewRender->AddSourceObject('aUserInput', $aUserInput);
$oViewRender->AddSourceObject('aFieldMessages', $aFieldMessages);
echo $oViewRender->Render('/common/userInput/form/formUserProfile.html.twig');
