<?php

$oViewRenderer = new ViewRenderer();
$oViewRenderer->AddSourceObject('id', $id);
$oViewRenderer->AddSourceObject('aHiddenFields', $aHiddenFields);
$oViewRenderer->AddSourceObject('fieldName', TGlobal::instance()->GetUserData('fieldName'));
$oViewRenderer->AddSourceObject('CKEditorFuncNum', TGlobal::instance()->GetUserData('CKEditorFuncNum'));
$oViewRenderer->AddSourceObject('sTable', $sTable);
$oViewRenderer->AddSourceObject('pathCmsController', PATH_CMS_CONTROLLER);

echo $oViewRenderer->Render('/MTTableManager/wysiwygImageChooser.html.twig');
