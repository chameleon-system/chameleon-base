<?php

/**
 * @deprecated since 6.2.0 - no longer used.
 */

// main layout
$layoutTemplate = 'empty';

// modules...
$moduleList = array('main' => array('model' => 'CMSSearch', 'view' => 'index', '_suppressHistory' => true));

// this line needs to be included... do not touch
if (!is_array($moduleList)) {
    $layoutTemplate = '';
}
