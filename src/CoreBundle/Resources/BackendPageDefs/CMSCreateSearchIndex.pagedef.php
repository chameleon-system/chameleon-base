<?php

/**
 * @deprecated since 6.2.0 - no longer used.
 */

// main layout
$layoutTemplate = 'popup_window_iframe';

// modules...
$moduleList = array('pagetitle' => array('model' => 'MTHeader', 'view' => 'title'), 'contentmodule' => array('model' => 'CMSSearch', 'view' => 'standard', '_suppressHistory' => true));

// this line needs to be included... do not touch
if (!is_array($moduleList)) {
    $layoutTemplate = '';
}
