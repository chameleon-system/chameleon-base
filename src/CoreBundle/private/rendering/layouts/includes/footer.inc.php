<?php

$oViewRender = new ViewRenderer();
$oViewRender->AddSourceObject('activeUser', TCMSUser::GetActiveUser() ? true : false);
$oViewRender->AddSourceObject('serverAddr', $_SERVER['SERVER_ADDR']);
echo $oViewRender->Render('BackendFooter/standard.html.twig');

require_once dirname(__FILE__).'/cms_footer_data.inc.php';
