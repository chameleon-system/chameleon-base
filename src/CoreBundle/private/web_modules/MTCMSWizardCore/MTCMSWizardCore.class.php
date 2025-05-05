<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class MTCMSWizardCore extends TUserCustomModelBase
{
    /**
     * @var TdbCmsWizardStep
     */
    protected $oActiveOrderStep;

    public const URL_PARAM_STEP_METHOD = 'cmswizardmethodcallname';
    public const URL_PARAM_STEP_SYSTEM_NAME = 'stepname';
    public const URL_PARAM_MODULE_SPOT = 'spot';

    public const SESSION_PARAM_NAME = 'MTCMSWizardCore';
    public const MSG_HANDLER_NAME = 'cmswizardmsg';

    /**
     * set to true if the user is executing a method.
     *
     * @var bool
     */
    protected $bExecutMethodCalled = false;

    protected $bAllowHTMLDivWrapping = true;

    /**
     * lazzy load method to fetch active step.
     */
    protected function LoadActiveStep()
    {
        if (is_null($this->oActiveOrderStep)) {
            // load current step - react to the call only if we are addressing the current module spot, or we are not addressing a module spot at all
            $sSpotName = $this->global->GetUserData(self::URL_PARAM_MODULE_SPOT);
            $sStepName = null;
            if (!$sSpotName || ($sSpotName && $sSpotName == $this->sModuleSpotName)) {
                $sStepName = $this->global->GetUserData(self::URL_PARAM_STEP_SYSTEM_NAME);
            }
            $this->oActiveOrderStep = TdbCmsWizardStep::GetStep($sStepName, $this->instanceID);
            if (is_null($this->oActiveOrderStep)) {
                // order step not found... go back to the caling step, but write a message
                $oMsgManager = TCMSMessageManager::GetInstance();
                $oMsgManager->AddMessage(TCMSMessageManager::GLOBAL_CONSUMER_NAME, 'SYSTEM-ERROR-SHOP-ORDER-STEP-NOT-DEFINED', ['target' => $sStepName, 'calling' => TdbCmsWizardStep::GetCallingStepName()]);
                $sStepName = TdbCmsWizardStep::GetCallingStepName();
                $this->oActiveOrderStep = TdbCmsWizardStep::GetStep($sStepName, $this->instanceID);
            }
            if (!is_null($this->oActiveOrderStep)) {
                $oOldList = TdbCmsWizardStepList::GetAllBeforeThisStep($this->oActiveOrderStep);
                $this->oActiveOrderStep->bIsActiveStep = true;
                $this->oActiveOrderStep->iCurrentPositionInList = $oOldList->Length() + 1;
                $this->oActiveOrderStep->Init();
            }
        }
    }

    public function Execute()
    {
        parent::Execute();
        $this->LoadActiveStep();
        $this->data['oActiveOrderStep'] = $this->oActiveOrderStep;
        $this->data['oStepNavi'] = TdbCmsWizardStepList::GetListNavigationForInstance($this->oActiveOrderStep);
        $this->data['sBasketRequestURL'] = self::GetCallingURL();

        $oWizardConf = TdbCmsWizardConfig::GetNewInstance();
        /** @var $oWizardConf TdbCmsWizardConfig */
        if (!$oWizardConf->LoadFromField('cms_tpl_module_instance_id', $this->instanceID)) {
            $oWizardConf = null;
        }
        $this->data['oWizardConf'] = $oWizardConf;

        return $this->data;
    }

    /**
     * return the url to the page that requested the step.
     *
     * @return string
     */
    public static function GetCallingURL()
    {
        $sURL = '';
        if (array_key_exists(self::SESSION_PARAM_NAME, $_SESSION)) {
            $sURL = $_SESSION[self::SESSION_PARAM_NAME];
        }

        return $sURL;
    }

    /**
     * save the url in the session so we can use it later to return to the calling page.
     *
     * @param string $sURL
     */
    public static function SetCallingURL($sURL)
    {
        $_SESSION[self::SESSION_PARAM_NAME] = $sURL;
    }

    /**
     * run a method on the step. default is ExecuteStep, but can be overwritten
     * by passing the parameter $sStepMethod (if null is passed, the method will try to fetch
     * the value from get/post from self::URL_PARAM_STEP_METHOD = xx.
     *
     * @param string $sStepMethod - method to execute. defaults to ExecuteStep
     */
    public function ExecuteStep($sStepMethod = null)
    {
        $this->bExecutMethodCalled = true;
        $this->LoadActiveStep();
        if (is_null($this->oActiveOrderStep)) {
            return false;
        } // stop if we have no active step

        if (is_null($sStepMethod)) {
            if ($this->global->UserDataExists(self::URL_PARAM_STEP_METHOD)) {
                $sStepMethod = $this->global->GetUserData(self::URL_PARAM_STEP_METHOD);
            }
        }
        if (is_null($sStepMethod) || false === $sStepMethod || empty($sStepMethod)) {
            $sStepMethod = 'ExecuteStep';
        }
        // check if the method is permitted
        $aAllowedMethod = $this->oActiveOrderStep->AllowedMethods();
        if (in_array($sStepMethod, $aAllowedMethod) && method_exists($this->oActiveOrderStep, $sStepMethod)) {
            $this->oActiveOrderStep->$sStepMethod();
        } else {
            // error - method not allowed
            $oMsgManager = TCMSMessageManager::GetInstance();
            $oMsgManager->AddMessage($this->sModuleSpotName, 'SYSTEM-ERROR-SHOP-ORDER-STEP-CALLED-METHOD-NOT-ALLOWED', ['methodName' => $sStepMethod]);
        }
    }

    /**
     * define any head includes the step needs.
     *
     * @return array
     */
    public function GetHtmlHeadIncludes()
    {
        $aIncludes = parent::GetHtmlHeadIncludes();
        if (!is_array($aIncludes)) {
            $aIncludes = [];
        }
        $this->LoadActiveStep();
        if (!is_null($this->oActiveOrderStep)) {
            $aStepIncludes = $this->oActiveOrderStep->GetHtmlHeadIncludes();
            if (count($aStepIncludes) > 0) {
                $aIncludes = array_merge($aIncludes, $aStepIncludes);
            }
        }

        $aIncludes = array_merge($aIncludes, $this->getResourcesForSnippetPackage('pkgShop/userInput'));
        $aIncludes = array_merge($aIncludes, $this->getResourcesForSnippetPackage('pkgExtranet'));

        return $aIncludes;
    }

    /**
     * add your custom methods as array to $this->methodCallAllowed here
     * to allow them to be called from web.
     */
    protected function DefineInterface()
    {
        parent::DefineInterface();
        $this->methodCallAllowed[] = 'ExecuteStep';
    }
}
