<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\Service\ActivePageServiceInterface;

/**
 * represents one step in the form wizzard.
 * /**/
class TFormWizardStep extends TCMSRecord
{
    protected $aInput;
    protected $aError;

    protected $bInputValid = false;

    protected $aRequiredFields = [];

    /**
     * return any variables you want available in your view in an assoc array here
     * the function will be called in the execute method of the wizard module.
     *
     * @return array
     */
    public function GetWizardViewData()
    {
        $aData = [];
        $aData['aRequiredFields'] = $this->aRequiredFields;

        return $aData;
    }

    /**
     * return true if the step is to be shown.
     *
     * @return bool
     */
    public function ShowStep()
    {
        return true;
    }

    /**
     * returns the step id following the current step.
     *
     * @param int $stepId - the item for which we want to get the next step (if not set, current step will be used)
     *
     * @return int
     */
    public function GetNextStepId($stepId = null)
    {
        $nextStepId = false;
        if (is_null($stepId)) {
            $stepId = $this->id;
        }
        // fetch next step
        $query = "SELECT *
                  FROM `module_wizard`
                 WHERE `cms_tpl_module_instance_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->sqlData['cms_tpl_module_instance_id'])."'
              ORDER BY `position`, `name`";
        $res = MySqlLegacySupport::getInstance()->query($query);
        $foundNode = false;
        while (!$foundNode && ($item = MySqlLegacySupport::getInstance()->fetch_assoc($res))) {
            if ($item['id'] == $stepId) {
                $foundNode = true;
            }
        }
        if ($foundNode) {
            // item found. next one must be the item in line
            if ($item = MySqlLegacySupport::getInstance()->fetch_assoc($res)) {
                $nextStepId = $item['id'];

                $sClassName = $item['class'];
                $oWizardStep = new $sClassName();
                /* @var $oWizardStep TFormWizardStep */
                $oWizardStep->LoadFromRow($item);
                // $oWizardStep->InitDefaults();
                if (!$oWizardStep->ShowStep()) {
                    $nextStepId = $oWizardStep->GetNextStepId($nextStepId);
                }
            }
        }

        return $nextStepId;
    }

    /**
     * @return string
     */
    public function GetLink()
    {
        return $this->getActivePageService()->getLinkToActivePageRelative([
            'state' => $this->id,
        ]);
    }

    /**
     * return the previous step id.
     *
     * @param int $stepId - the item for which we want to get the previous step (if not set, current step will be used)
     *
     * @return int
     */
    public function GetPreviousStepId($stepId = null)
    {
        $iPreviousStepId = false;
        if (is_null($stepId)) {
            $stepId = $this->id;
        }
        $query = "SELECT *
                  FROM `module_wizard`
                 WHERE `cms_tpl_module_instance_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->sqlData['cms_tpl_module_instance_id'])."'
              ORDER BY `position`, `name`";
        $res = MySqlLegacySupport::getInstance()->query($query);
        $previousNode = null;
        $foundNode = false;
        while (!$foundNode && ($item = MySqlLegacySupport::getInstance()->fetch_assoc($res))) {
            if ($item['id'] == $stepId) {
                $foundNode = true;
            } else {
                $previousNode = $item;
            }
        }
        if ($foundNode && !is_null($previousNode)) {
            $iPreviousStepId = $previousNode['id'];

            $sClassName = $previousNode['class'];

            $oWizardStep = new $sClassName();
            /* @var $oWizardStep TFormWizardStep */
            $oWizardStep->LoadFromRow($previousNode);
            // $oWizardStep->InitDefaults();
            if (!$oWizardStep->ShowStep()) {
                $iPreviousStepId = $oWizardStep->GetPreviousStepId($iPreviousStepId);
            }
        }

        return $iPreviousStepId;
    }

    /**
     * @param int $iStepId
     * @param array $aParameters
     */
    public function RedirectToStep($iStepId, $aParameters = [])
    {
        $aParameters['state'] = $iStepId;
        $this->getRedirect()->redirectToActivePage($aParameters);
    }

    public function ProcessData()
    {
        $this->LoadPostData();
        $this->ProcessDataHook();
        $this->StoreInSession();
        if ($this->ValidateData()) {
            $this->OnValidUserData();
            $this->bInputValid = true;
        } else {
            $this->OnInvalidUserData();
            $this->bInputValid = false;
        }

        return $this->bInputValid;
    }

    protected function ProcessDataHook()
    {
    }

    protected function LoadPostData()
    {
        $oGlobal = TGlobal::instance();
        if ($oGlobal->UserDataExists('aInput')) {
            $this->aInput = $oGlobal->GetUserData('aInput');
            if (!is_array($this->aInput)) {
                $this->aInput = [];
            }
        }
    }

    public function GetUserInput()
    {
        return $this->aInput;
    }

    public function GetErrors()
    {
        if (is_array($this->aError) && count($this->aError) > 0) {
            return $this->aError;
        } else {
            return false;
        }
    }

    public function GetView()
    {
        return 'standard';
    }

    public function InitDefaults()
    {
        $bDataFromSession = $this->FetchFromSession();
        if (!$bDataFromSession || !is_array($this->aInput)) {
            $this->aInput = [];
        }
        $this->aError = [];

        return $bDataFromSession;
    }

    protected function ValidateData()
    {
        foreach ($this->aRequiredFields as $aRequiredField) {
            if (is_array($this->aInput) && array_key_exists($aRequiredField, $this->aInput)) {
                $this->aInput[$aRequiredField] = trim($this->aInput[$aRequiredField]);
                if (empty($this->aInput[$aRequiredField])) {
                    $this->aError[$aRequiredField] = true;
                }
            } else {
                $this->aError[$aRequiredField] = true;
            }
        }
        reset($this->aRequiredFields);
        $isValid = (count($this->aError) < 1);

        return $isValid;
    }

    protected function OnInvalidUserData()
    {
    }

    protected function OnValidUserData()
    {
    }

    protected function SessionStepName()
    {
        $sKeyName = '_wizardstep'.$this->id;
        if (array_key_exists('name_internal', $this->sqlData) && !empty($this->sqlData['name_internal'])) {
            $sKeyName = $this->sqlData['name_internal'];
        }

        return $sKeyName;
    }

    public function StoreInSession()
    {
        if (!array_key_exists('_TFormWizardSteps', $_SESSION)) {
            $_SESSION['_TFormWizardSteps'] = [];
        }
        $_SESSION['_TFormWizardSteps'][$this->SessionStepName()] = $this->aInput;
    }

    public function ResetSession()
    {
        $_SESSION['_TFormWizardSteps'] = [];
    }

    public function FetchFromSession()
    {
        if (!array_key_exists('_TFormWizardSteps', $_SESSION)) {
            $_SESSION['_TFormWizardSteps'] = [];
        }
        if (array_key_exists($this->SessionStepName(), $_SESSION['_TFormWizardSteps'])) {
            $this->aInput = $_SESSION['_TFormWizardSteps'][$this->SessionStepName()];

            return true;
        } else {
            return false;
        }
    }

    public function FieldError($sFieldName)
    {
        if (is_array($this->aError) && array_key_exists($sFieldName, $this->aError)) {
            return $this->aError[$sFieldName];
        } else {
            return false;
        }
    }

    public function IsRequiredField($sFieldName)
    {
        return in_array($sFieldName, $this->aRequiredFields);
    }

    public function GetFieldValue($sFieldName)
    {
        $sFieldValue = '';
        if (is_array($this->aInput) && array_key_exists($sFieldName, $this->aInput)) {
            $sFieldValue = $this->aInput[$sFieldName];
        } else {
            // try to fetch from global
            $oGlobal = TGlobal::instance();
            if ($oGlobal->UserDataExists('aInput')) {
                $tmpInput = $oGlobal->GetUserData('aInput');
                if (is_array($tmpInput) && array_key_exists($sFieldName, $tmpInput)) {
                    $sFieldValue = $tmpInput[$sFieldName];
                }
            }
        }

        return $sFieldValue;
    }

    protected function GetStepDataFromSession($sInternalName)
    {
        $aData = false;
        $sFullKeyName = $sInternalName;
        if (array_key_exists($sFullKeyName, $_SESSION['_TFormWizardSteps'])) {
            $aData = $_SESSION['_TFormWizardSteps'][$sFullKeyName];
        }

        return $aData;
    }

    /**
     * returns the link to a step with internal name $sName within the wizard.
     *
     * @param string $sName
     * @param bool $bSearchAllInstances - if set to true, the class will search for a match in all instances
     *                                  (it will still prefere the current instance, but searches in the other
     *                                  instances as a fallback)
     *
     * @return string|bool
     */
    public function GetStepLink($sName, $bSearchAllInstances = false)
    {
        $sLink = false;
        $query = "SELECT *
                  FROM `module_wizard`
                 WHERE `cms_tpl_module_instance_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->sqlData['cms_tpl_module_instance_id'])."'
                   AND `name_internal` = '".MySqlLegacySupport::getInstance()->real_escape_string($sName)."'";
        if ($trow = MySqlLegacySupport::getInstance()->fetch_assoc(MySqlLegacySupport::getInstance()->query($query))) {
            $sLink = $this->getActivePageService()->getLinkToActivePageRelative([
                'state' => $trow['id'],
            ]);
        } elseif ($bSearchAllInstances) {
            $query = "SELECT *
                    FROM `module_wizard`
                   WHERE `name_internal` = '".MySqlLegacySupport::getInstance()->real_escape_string($sName)."'";
            if ($trow = MySqlLegacySupport::getInstance()->fetch_assoc(MySqlLegacySupport::getInstance()->query($query))) {
                // get the page that holds the instance
                $query = "SELECT * FROM cms_tpl_page_cms_master_pagedef_spot WHERE `cms_tpl_module_instance_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($trow['cms_tpl_module_instance_id'])."'";
                if ($aPageSpot = MySqlLegacySupport::getInstance()->fetch_assoc(MySqlLegacySupport::getInstance()->query($query))) {
                    self::getPageService()->getLinkToPageRelative($aPageSpot['cms_tpl_page_id'], [
                        'state' => $trow['id'],
                    ]);
                }
            }
        }

        return $sLink;
    }

    protected function GetAllStepData()
    {
        $aData = false;
        if (array_key_exists('_TFormWizardSteps', $_SESSION)) {
            $aData = $_SESSION['_TFormWizardSteps'];
        }

        return $aData;
    }

    /**
     * any head includes needed for the step.
     *
     * @return array
     */
    public function GetHtmlHeadIncludes()
    {
        return [];
    }

    /**
     * @return ActivePageServiceInterface
     */
    private function getActivePageService()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.active_page_service');
    }

    /**
     * @return ICmsCoreRedirect
     */
    private function getRedirect()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.redirect');
    }
}
