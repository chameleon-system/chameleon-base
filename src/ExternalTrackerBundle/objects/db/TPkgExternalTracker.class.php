<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TPkgExternalTracker extends TPkgExternalTrackerAutoParent
{
    /**
     * factory creates a new instance and returns it.
     *
     * @param string|array $sData - either the id of the object to load, or the row with which the instance should be initialized
     * @param string $sLanguage - init with the language passed
     */
    public static function GetNewInstance($sData = null, $sLanguage = null): TdbPkgExternalTracker
    {
        $oObject = parent::GetNewInstance($sData, $sLanguage);
        if ($oObject && $oObject->sqlData && is_array($oObject->sqlData)) {
            if (!empty($oObject->fieldClass)) {
                $sClassName = $oObject->fieldClass;
                $oNewObject = new $sClassName();
                $oNewObject->LoadFromRow($oObject->sqlData);
                $oObject = $oNewObject;
            }
        }

        return $oObject;
    }

    /**
     * @return string[]
     */
    public function GetHTMLHeadIncludes(TPkgExternalTrackerState $oState)
    {
        return [];
    }

    /**
     * @return string[]
     */
    public function GetPostBodyOpeningCode(TPkgExternalTrackerState $oState)
    {
        return [];
    }

    /**
     * @return string[]
     */
    public function GetPreBodyClosingCode(TPkgExternalTrackerState $oState)
    {
        return [];
    }

    /**
     * @return bool
     */
    public function IsActive()
    {
        return $this->fieldActive;
    }

    /**
     * this method is called after successful record loading
     * set demo-code if tracker is in demo mode.
     *
     * @return void
     */
    protected function PostLoadHook()
    {
        parent::PostLoadHook();
        $demoMode = ChameleonSystem\CoreBundle\ServiceLocator::getParameter('chameleon_system_external_tracker.demo_mode');
        if (!TGlobal::IsCMSMode() && $demoMode) {
            if (isset($this->sqlData['test_identifier'])) {
                $this->fieldIdentifier = $this->fieldTestIdentifier;
                $this->sqlData['identifier'] = $this->sqlData['test_identifier'];
            }
        }
    }
}
