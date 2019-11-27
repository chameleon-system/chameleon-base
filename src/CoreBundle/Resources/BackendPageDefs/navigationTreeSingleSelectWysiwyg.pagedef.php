<?php

$layoutTemplate = 'popup_iframe';
$moduleList = array(
    'contentmodule' => array(
        'model' => 'chameleon_system_core.module.navigation_tree_single_select_wysiwyg',
        'view' => 'standard',
    ),
);

addDefaultPageTitle($moduleList);
