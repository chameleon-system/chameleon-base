<?php
/**
 * @deprecated since 6.2.0 - Chameleon has a new media manager
 */
// main layout
$layoutTemplate = 'mediaManager';

// modules...
$moduleList = array('pagetitle' => array('model' => 'MTHeader', 'view' => 'title'), 'content' => array('model' => 'CMSMediaManager', 'view' => 'standard'));

// this line needs to be included... do not touch
if (!is_array($moduleList)) {
    $layoutTemplate = '';
}
