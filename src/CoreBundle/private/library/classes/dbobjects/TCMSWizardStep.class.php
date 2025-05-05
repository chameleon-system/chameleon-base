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
use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;
use ChameleonSystem\ExtranetBundle\Interfaces\ExtranetUserProviderInterface;
use Symfony\Component\HttpFoundation\Request;

class TCMSWizardStep extends TAdbCmsWizardStep
{
    public const SESSION_KEY_NAME = 'esono/core/_TCMSWizardStep';

    /**
     * internal cache for field infos (text and name infos for each field that can be set via the wizard config).
     *
     * @var array
     */
    protected $aFieldInfo;

    /**
     * set to true for the currently active step.
     *
     * @var bool
     */
    public $bIsActiveStep = false;

    /**
     * set to true if this step comes before the current active step.
     *
     * @var bool
     */
    public $bIsBeforeActiveStep = false;

    /**
     * set to true if this step comes after the current active step.
     *
     * @var bool
     */
    public $bIsAfterActiveStep = false;

    /**
     * set to the current position within the curren wizard
     * set to false if the position has not been set.
     *
     * @var int
     */
    public $iCurrentPositionInList = false;

    /**
     * set this to false if a step should not be listes in the navi.
     *
     * @var bool
     */
    public $bShowInNavi = true;

    /**
     * the wizard conf.
     *
     * @var TdbCmsWizardConfig
     */
    protected $oWizardConf;

    /**
     * store all view data here so that it can be used in the description text.
     *
     * @var array
     */
    protected $aViewData = [];

    /**
     * return string shown over wizard navi.
     *
     * @return string
     */
    public function GetPreStepInfo()
    {
        return '';
    }

    /**
     * method is called from the init method of the calling module. here you can check
     * if the step may be viewed, and redirect to another step if the user does not have permission.
     */
    public function Init()
    {
    }

    /**
     * returns the step with the systemname $sStepName (language id is taken form the active page object).
     *
     * @param string $sStepName
     *
     * @return TdbCmsWizardStep
     */
    public static function GetStep($sStepName, $iInstanceId)
    {
        $oStep = null;
        $oStepData = null;
        if (is_null($sStepName) || false === $sStepName || empty($sStepName)) {
            // fetch the first step instead..
            $oSteps = TdbCmsWizardStepList::GetCmsWizardStepListForCmsTplModuleInstance($iInstanceId);
            if ($oSteps->Length() > 0) {
                $oStepData = $oSteps->Current();
            }
            unset($oSteps);
        } else {
            $oStepData = TdbCmsWizardStep::GetNewInstance();
            /** @var $oStepData TdbCmsWizardStep */
            if (!$oStepData->LoadFromFields(['systemname' => $sStepName, 'cms_tpl_module_instance_id' => $iInstanceId])) {
                $oStepData = null;
            }
        }
        if (!is_null($oStepData)) {
            $sClassName = $oStepData->fieldClass;
            /**
             * @var TdbCMSWizardStep $oStep
             */
            $oStep = new $sClassName();
            $activeLanguageId = self::getLanguageService()->getActiveLanguageId();
            $oStep->SetLanguage($activeLanguageId);
            $oStep->LoadFromRow($oStepData->sqlData);
        }

        return $oStep;
    }

    /**
     * return the calling step name (set by JumpToStep).
     *
     * @return string
     */
    public static function GetCallingStepName()
    {
        $sCallingStepName = null;
        if (array_key_exists(self::SESSION_KEY_NAME, $_SESSION)) {
            $sCallingStepName = $_SESSION[self::SESSION_KEY_NAME];
        }

        return $sCallingStepName;
    }

    /**
     * @param TdbCmsWizardStep $oStep
     *                                redirects to the step. the step calling this method will store its name in the session,
     *                                so that the new step knows where to return to
     */
    protected function JumpToStep(TdbCmsWizardStep $oStep)
    {
        $_SESSION[self::SESSION_KEY_NAME] = $this->fieldSystemname;
        $this->getRedirect()->redirect($oStep->GetStepURL());
    }

    /**
     * reloads the current step.
     */
    protected function ReloadCurrentStep()
    {
        $this->getRedirect()->redirect($this->GetStepURL());
    }

    /**
     * return url to this step.
     *
     * @return string
     */
    public function GetStepURL()
    {
        $oGlobal = TGlobal::instance();
        $sSpotName = $oGlobal->GetExecutingModulePointer()->sModuleSpotName;

        return $this->getActivePageService()->getLinkToActivePageRelative([
            MTCMSWizardCore::URL_PARAM_STEP_SYSTEM_NAME => $this->fieldSystemname,
            MTCMSWizardCore::URL_PARAM_MODULE_SPOT => $sSpotName,
        ]);
    }

    /**
     * return link to next step.
     *
     * @return string
     */
    public function GetNextStepULR()
    {
        $sLink = false;
        $oNext = $this->GetNextStep();
        if (!is_null($oNext)) {
            $sLink = $oNext->GetStepURL();
        }

        return $sLink;
    }

    /**
     * returns the link to the previous step (or false if there is none).
     *
     * @return string
     */
    protected function GetReturnToLastStepURL()
    {
        $sLink = false;
        $oBackItem = $this->GetPreviousStep();
        if (!is_null($oBackItem)) {
            $sLink = $oBackItem->GetStepURL();
        } else {
            $oWizardConf = $this->GetWizardConf();
            if (!is_null($oWizardConf)) {
                if (!empty($oWizardConf->fieldFirstStepReturnNode)) {
                    $oNode = new TCMSTreeNode();
                    /** @var $oNode TCMSTreeNode */
                    if ($oNode->Load($oWizardConf->fieldFirstStepReturnNode)) {
                        $sLink = static::getTreeService()->getLinkToPageForTreeRelative($oNode);
                    }
                }
            }
        }

        return $sLink;
    }

    /**
     * executes the current step. when no errors occure, the step redirects to the next step in line.
     */
    public function ExecuteStep()
    {
        if ($this->ProcessStep()) {
            $oNextStep = $this->GetNextStep();
            $this->JumpToStep($oNextStep);
        }
    }

    /**
     * called by the ExecuteStep Method - place any logic for the standard proccessing of this step here
     * return false if any errors occur (returns the user to the current step for corrections).
     *
     * @return bool
     */
    protected function ProcessStep()
    {
        if (Request::METHOD_POST !== $this->getRequest()->getMethod()) {
            throw new LogicException('Wizard step forms may only be submitted using the POST method. Please adjust your form accordingly.');
        }

        return true;
    }

    /**
     * return name of next step.
     *
     * @return TdbCmsWizardStep
     */
    protected function GetNextStep()
    {
        static $oNextStep;
        if (!$oNextStep) {
            $oNextStep = TdbCmsWizardStepList::GetNextStep($this);
        }

        return $oNextStep;
    }

    /**
     * return the previous step (null if this is the first step).
     *
     * @return TdbCmsWizardStep
     */
    protected function GetPreviousStep()
    {
        static $oPreviousStep;
        if (!$oPreviousStep) {
            $oPreviousStep = TdbCmsWizardStepList::GetPreviousStep($this);
        }

        return $oPreviousStep;
    }

    /**
     * define any methods of the class that may be called via get or post.
     *
     * @return array
     */
    public function AllowedMethods()
    {
        return ['ExecuteStep'];
    }

    /**
     * define any head includes the step needs.
     *
     * @return array
     */
    public function GetHtmlHeadIncludes()
    {
        return [];
    }

    /**
     * return the view to use for the render method. overwrite this if you want to return
     * a view different from the one set in the database.
     *
     * @return string
     */
    protected function GetRenderViewName()
    {
        return $this->fieldRenderViewName;
    }

    /**
     * return the view to use for the render method. overwrite this if you want to return
     * a view different from the one set in the database (must return Core, Custom-Core, or Customer.
     *
     * @return string
     */
    protected function GetRenderViewType()
    {
        return $this->fieldRenderViewType;
    }

    /**
     * return the wizard conf.
     *
     * @return TdbCmsWizardConfig
     */
    protected function GetWizardConf()
    {
        if (is_null($this->oWizardConf)) {
            $this->oWizardConf = TdbCmsWizardConfig::GetNewInstance();
            /* @var $oWizardConf TdbCmsWizardConfig */
            if (!$this->oWizardConf->LoadFromField('cms_tpl_module_instance_id', $this->fieldCmsTplModuleInstanceId)) {
                $this->oWizardConf = null;
            }
        }

        return $this->oWizardConf;
    }

    /**
     * used to display the step.
     *
     * @param string $sSpotName - name of the spot the step is in
     * @param array $aCallTimeVars - place any custom vars that you want to pass through the call here
     * @param string $sViewName - optional view - will be loaded from db if not set
     * @param bool $bTriggerClearCache - clear any render cache for this view
     *
     * @return string
     */
    public function Render($sSpotName = null, $aCallTimeVars = [], $sViewName = null, $bTriggerClearCache = false)
    {
        $oView = new TViewParser();

        $oStepNext = $this->GetNextStep();
        $oStepPrevious = $this->GetPreviousStep();
        $extranetUser = $this->getExtranetUserProvider()->getActiveUser();
        $oView->AddVar('oUser', $extranetUser);

        $oExtranetConfig = TdbDataExtranet::GetInstance();
        $oView->AddVar('oExtranetConfig', $oExtranetConfig);
        $oView->AddVar('oStep', $this);
        $oView->AddVar('oStepNext', $oStepNext);
        $oView->AddVar('oStepPrevious', $oStepPrevious);

        $aViewVariables['oWizardConf'] = $this->GetWizardConf();

        $sBackLink = $this->GetReturnToLastStepURL();
        $this->aViewData['sBackLink'] = $sBackLink;
        $oView->AddVar('sBackLink', $sBackLink);
        $oView->AddVar('sSpotName', $sSpotName);
        $oView->AddVar('aCallTimeVars', $aCallTimeVars);

        if (null === $sViewName) {
            $sViewName = $this->GetRenderViewName();
        }
        $sViewType = $this->GetRenderViewType();
        $aOtherParameters = $this->GetAdditionalViewVariables($sViewName, $sViewType);
        $this->aViewData = array_merge($this->aViewData, $aOtherParameters);

        $oView->AddVarArray($aOtherParameters);

        $sDescription = $this->GetDescription();
        $this->aViewData['sDescription'] = $sDescription;
        $oView->AddVar('sDescription', $sDescription);

        $sHTML = '';
        if ($this->fieldIsPackage) {
            $sHTML = $oView->RenderObjectPackageView($sViewName, $this->fieldRenderViewSubtype, $sViewType);
        } else {
            $sHTML = $oView->RenderObjectView($sViewName, $this->fieldRenderViewSubtype, $sViewType);
        }

        return $sHTML;
    }

    /**
     * Enter description here...
     */
    protected function GetDescription()
    {
        return $this->GetTextField('description', 600, true, $this->aViewData);
    }

    /**
     * use this method to add any variables to the render method that you may
     * require for some view.
     *
     * @param string $sViewName - the view being requested
     * @param string $sViewType - the location of the view (Core, Custom-Core, Customer)
     *
     * @return array
     */
    protected function GetAdditionalViewVariables($sViewName, $sViewType)
    {
        $aViewVariables = [];

        return $aViewVariables;
    }

    /**
     * @return ExtranetUserProviderInterface
     */
    private function getExtranetUserProvider()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_extranet.extranet_user_provider');
    }

    /**
     * @return ActivePageServiceInterface
     */
    private function getActivePageService()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.active_page_service');
    }

    /**
     * @return InputFilterUtilInterface
     */
    protected function getInputFilterUtil()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.util.input_filter');
    }

    /**
     * @return Request|null
     */
    protected function getRequest()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('request_stack')->getCurrentRequest();
    }

    /**
     * @return ICmsCoreRedirect
     */
    private function getRedirect()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.redirect');
    }
}
