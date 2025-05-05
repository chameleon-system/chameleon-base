<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * the classes for each step should be located @ ./extensions/library/classes/components/FormWizard
 * NOTE: Class is outdated - for new wizards, use TCMSWizardCore instead.
 * /**/
class MTWizardCore extends TUserCustomModelBase
{
    protected $iState;
    protected $iPreviousState;
    /**
     * the wizard step.
     *
     * @var TFormWizardStep
     */
    protected $oWizardStep;

    /**
     * a TIterator holding all items up to the current step.
     *
     * @var TIterator
     */
    protected $oWizardPath;

    protected $bAllowHTMLDivWrapping = true;

    public function Init()
    {
        parent::Init();
        // fetch state
        if ($this->global->UserDataExists('state')) {
            $this->iState = $this->global->GetUserData('state');
        } else {
            // fetch first state from list
            $query = "SELECT `id`  FROM `module_wizard` WHERE `cms_tpl_module_instance_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->instanceID)."' ORDER BY `position`, `name` LIMIT 0,1";
            if ($aState = MySqlLegacySupport::getInstance()->fetch_assoc(MySqlLegacySupport::getInstance()->query($query))) {
                $this->iState = $aState['id'];
            } else {
                trigger_error('No States defined for this instance of the wizard', E_USER_WARNING);
            }
        }
        $this->LoadWizardObject();
        $this->LoadStepBreadcrumb();
    }

    public function GetClassName()
    {
        return 'MTWizardCore';
    }

    protected function LoadWizardObject()
    {
        if (!is_null($this->iState)) {
            $query = "SELECT *
                    FROM `module_wizard`
                   WHERE `cms_tpl_module_instance_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->instanceID)."'
                     AND `id` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->iState)."'
                 ";
            if ($aState = MySqlLegacySupport::getInstance()->fetch_assoc(MySqlLegacySupport::getInstance()->query($query))) {
                $sClassName = $aState['class'];
                $this->oWizardStep = new $sClassName();
                $this->oWizardStep->LoadFromRow($aState);
                $this->oWizardStep->InitDefaults();
                $this->SetTemplate($this->GetClassName(), $this->oWizardStep->GetView());
            }

            // now fetch back link
            $previousNode = $this->oWizardStep->GetPreviousStepId();
            if (false !== $previousNode) {
                $this->iPreviousState = $previousNode;
            } else {
                $this->iPreviousState = null;
            }
        }
    }

    protected function DefineInterface()
    {
        parent::DefineInterface();
        $externalFunctions = ['PostData'];
        $this->methodCallAllowed = array_merge($this->methodCallAllowed, $externalFunctions);
    }

    public function PostData()
    {
        if ($this->oWizardStep->ProcessData()) {
            // redirect to next step
            $iNextStep = $this->oWizardStep->GetNextStepId();
            // if ($iNextStep === false) $iNextStep = $this->iState;
            if (false !== $iNextStep) {
                $this->RedirectToState($iNextStep);
            }
        }
    }

    /**
     * @param int $iState
     * @param array|null $aOptionalData
     */
    protected function RedirectToState($iState, $aOptionalData = null)
    {
        $this->iState = $iState;
        if (is_null($aOptionalData)) {
            $aOptionalData = [];
        }
        $aOptionalData['state'] = $this->iState;

        $this->getRedirect()->redirectToActivePage($aOptionalData);
    }

    protected function GetStepIdByName($sStepName)
    {
        $query = "SELECT *
                  FROM `module_wizard`
                 WHERE `cms_tpl_module_instance_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->instanceID)."'
                   AND `name_internal` = '".MySqlLegacySupport::getInstance()->real_escape_string($sStepName)."'";
        if ($trow = MySqlLegacySupport::getInstance()->fetch_assoc(MySqlLegacySupport::getInstance()->query($query))) {
            return $trow['id'];
        } else {
            return null;
        }
    }

    /**
     * fetches all wizard steps up to the current active step.
     */
    protected function LoadStepBreadcrumb()
    {
        $this->oWizardPath = new TIterator();
        $query = "SELECT *
                  FROM `module_wizard`
                 WHERE `cms_tpl_module_instance_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->instanceID)."'
              ORDER BY `position`, `name`";
        $res = MySqlLegacySupport::getInstance()->query($query);
        $foundNode = false;
        while (!$foundNode && ($item = MySqlLegacySupport::getInstance()->fetch_assoc($res))) {
            $sClassName = $item['class'];
            $oWizardStep = new $sClassName();
            $oWizardStep->LoadFromRow($item);
            $this->oWizardPath->AddItem($oWizardStep);
            if ($item['id'] == $this->iState) {
                $foundNode = true;
            }
        }
    }

    public function Execute()
    {
        parent::Execute();
        if (!is_null($this->oWizardStep)) {
            $this->data['aInput'] = $this->oWizardStep->GetUserInput();
            $this->data['aError'] = $this->oWizardStep->GetErrors();
            $this->data['oWizardStep'] = $this->oWizardStep;
            $this->data['iState'] = $this->iState;
            $this->data['iPreviousState'] = $this->iPreviousState;
            $aWizardStepData = $this->oWizardStep->GetWizardViewData();
            $this->data = array_merge($this->data, $aWizardStepData);
        }

        return $this->data;
    }

    public function GetHtmlHeadIncludes()
    {
        $aIncludes = parent::GetHtmlHeadIncludes();

        // add include for current step...
        if (!is_null($this->oWizardStep)) {
            $aStepIncludes = $this->oWizardStep->GetHtmlHeadIncludes();
            $aIncludes = array_merge($aIncludes, $aStepIncludes);
        }

        return $aIncludes;
    }

    public function _AllowCache()
    {
        return false;
    }

    /**
     * @return ICmsCoreRedirect
     */
    private function getRedirect()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.redirect');
    }
}
