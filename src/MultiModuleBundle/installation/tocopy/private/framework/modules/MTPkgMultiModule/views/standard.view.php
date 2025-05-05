<?php

/** @var $sModuleSpotName string */
if (isset($aModuleInstances) && is_array($aModuleInstances)) {
    /** @var $oModuleInstance TdbCmsTplModuleInstance */
    foreach ($aModuleInstances as $oModuleInstance) {
        $aAdditionalData = [];
        if (array_key_exists('show_full', $oModuleInstance->sqlData)) {
            $aAdditionalData['show_full'] = $oModuleInstance->sqlData['show_full'];
        }
        echo $oModuleInstance->Render($aAdditionalData, $aModuleInstanceSpots[$oModuleInstance->id]);
    }
}
