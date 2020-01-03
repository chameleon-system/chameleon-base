<?php
/**
 * @deprecated since 6.3.8 - use navigationTreeSingleSelectWysiwyg.pagedef.php instead
 */
$layoutTemplate = 'popup_iframe';
$moduleList = array(
    'contentmodule' => array(
        'model' => 'CMSTreeNodeSelectWYSIWYG',
        'view' => 'standard',
    ),
);

addDefaultPageTitle($moduleList);
