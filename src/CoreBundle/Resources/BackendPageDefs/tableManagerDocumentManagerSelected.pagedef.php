<?php

$layoutTemplate = 'frame';
$moduleList = array('contentmodule' => array('model' => 'MTTableManager', 'view' => 'wysiwygImageChooser', '_suppressHistory' => true, 'listClass' => 'TCMSListManagerDocumentManagerSelected'));

addDefaultPageTitle($moduleList);
