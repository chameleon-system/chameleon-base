<?php

/**
 * @deprecated since 6.2.0 - no longer used.
 */
$layoutTemplate = 'popup_window_iframe';
$moduleList = [
    'contentmodule' => [
        'model' => 'CMSSearch',
        'view' => 'standard',
        '_suppressHistory' => true,
    ],
];

addDefaultPageTitle($moduleList);
