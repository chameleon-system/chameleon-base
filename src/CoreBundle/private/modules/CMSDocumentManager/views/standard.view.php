<?php

$oViewRenderer = new ViewRenderer();
$oViewRenderer->AddSourceObject('id', $id);
$oViewRenderer->AddSourceObject('recordID', (false != $recordID) ? $recordID : (''));
$oViewRenderer->AddSourceObject('mltTable', (isset($mltTable)) ? $mltTable : (''));
$oViewRenderer->AddSourceObject('fieldName', (false != $fieldName) ? $fieldName : (''));
$oViewRenderer->AddSourceObject('tableName', (false != $tableName) ? $tableName : (''));
$oViewRenderer->AddSourceObject('tableID', (false != $tableID) ? $tableID : (''));
$oViewRenderer->AddSourceObject('wysiwygMode', (isset($wysiwygMode)) ? $wysiwygMode : (''));

$oViewRenderer->AddSourceObject('sPathTheme', TGlobal::GetPathTheme());
$oViewRenderer->AddSourceObject('sStaticUrlToWebLibCross', TGlobal::GetStaticURLToWebLib('/images/icons/cross.png'));
$oViewRenderer->AddSourceObject('sStaticUrlToWebLibActionRefresh', TGlobal::GetStaticURLToWebLib('/images/icons/action_refresh.gif'));

$oViewRenderer->AddSourceObject('CKEditorFuncNum', $CKEditorFuncNum);
$oViewRenderer->AddSourceObject('pathCmsController', PATH_CMS_CONTROLLER);

echo $oViewRenderer->Render('CMSDocumentManager/standard.html.twig');

require_once dirname(__FILE__).'/includes/javascripts.inc.php';
