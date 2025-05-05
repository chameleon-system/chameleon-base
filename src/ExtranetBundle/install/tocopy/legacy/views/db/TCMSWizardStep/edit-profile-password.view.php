<?php

$oExtranetConfig = TdbDataExtranet::GetInstance();
$oViewRender = new ViewRenderer();
$oViewRender->AddMapper(new TCMSWizardStepMapper_UserProfilePassword());
$oViewRender->AddSourceObject('sSpotName', $oExtranetConfig->fieldExtranetSpotName);
$oViewRender->AddSourceObject('oObject', $oStep);
$oViewRender->AddSourceObject('sWizardModuleModuleSpot', MTCMSWizardCore::URL_PARAM_MODULE_SPOT);
$oViewRender->AddSourceObject('sCustomMSGConsumer', 'editProfilePassword');
echo $oViewRender->Render('/common/userInput/form/formUserProfilePassword.html.twig');
