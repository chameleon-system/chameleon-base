<?php

$oViewRenderer = new ViewRenderer();
$oViewRenderer->AddSourceObject('sImageTableConfId', $id);
$oViewRenderer->AddSourceObject('sImageTableConfCmsident', $cmsident);
$oViewRenderer->AddSourceObject('sAllowedFileTypes', $sAllowedFileTypes);
$oViewRenderer->AddSourceObject('CKEditor', $CKEditor);
$oViewRenderer->AddSourceObject('CKEditorFuncNum', $CKEditorFuncNum);
$oViewRenderer->AddSourceObject('langCode', $langCode);
$oViewRenderer->AddSourceObject('mediaTreeSelectBox', $mediaTreeSelectBox);
$oViewRenderer->AddSourceObject('maxUploadSize', $maxUploadSize);
$oViewRenderer->AddSourceObject('sButton', TCMSRender::DrawButton(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.cms_module_wysiwyg_image.upload'), 'javascript:openUploadDialog();', 'fas fa-upload'));
$oViewRenderer->AddSourceObject('pathCmsController', PATH_CMS_CONTROLLER);
echo $oViewRenderer->Render('CMSModuleWYSIWYGImage/standard.html.twig');
