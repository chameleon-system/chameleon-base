<?php
/**
 * @deprecated since 6.2.0 - Chameleon has a new media manager
 */
// main layout
$layoutTemplate = 'JSON';

// modules...
$moduleList = array('module' => array('model' => 'CMSMediaManagerTreeRPC', 'view' => 'standard'));

// this line needs to be included... do not touch
if (!is_array($moduleList)) {
    $layoutTemplate = '';
}
