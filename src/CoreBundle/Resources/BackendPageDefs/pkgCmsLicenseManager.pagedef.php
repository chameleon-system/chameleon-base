<?php
/**
 * @deprecated since 6.3.0 - not used anymore.
 */
// main layout
$layoutTemplate = 'default';

// modules...
$moduleList = array('pagetitle' => array('model' => 'MTHeader', 'view' => 'title'), 'headerimage' => array('model' => 'MTHeader', 'view' => 'standard'),
                    'contentmodule' => array('model' => 'TPkgCmsLicenseManager_MTLicenseManager', 'view' => 'standard'), );

// this line needs to be included... do not touch
if (!is_array($moduleList)) {
    $layoutTemplate = '';
}
