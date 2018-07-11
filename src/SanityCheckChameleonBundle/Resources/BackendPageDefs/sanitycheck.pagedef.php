<?php

// main layout
$layoutTemplate = 'popup_window_iframe';

// modules...
$moduleList = array(
    'pagetitle' => array('model' => 'MTHeader', 'view' => 'title'),
    'contentmodule' => array(
        'model' => 'chameleon_system_sanity_check_chameleon.module.cms_sanity_check',
        'view' => 'standard',
        'moduleType' => '@ChameleonSystemSanityCheckChameleonBundle',
        '_suppressHistory' => true,
    ),
);

// this line needs to be included... do not touch
if (!is_array($moduleList)) {
    $layoutTemplate = '';
}
