<?php

$oViewRender = new ViewRenderer();
echo $oViewRender->Render('BackendFooter/standard.html.twig');

require_once __DIR__.'/cms_footer_data.inc.php';