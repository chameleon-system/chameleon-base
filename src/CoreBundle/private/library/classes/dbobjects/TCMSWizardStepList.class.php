<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TCMSWizardStepList extends TAdbCmsWizardStepList
{
    /**
     * if the list holds steps for a module instance, then the instance id will be available here.
     *
     * @var int
     */
    public $iModuleInstanceId;

    /**
     * factory returning an element for the list.
     *
     * @param array $aData
     */
    protected function _NewElement($aData): TdbCmsWizardStep
    {
        return TdbCmsWizardStep::GetStep($aData['systemname'], $aData['cms_tpl_module_instance_id']);
    }

    /**
     * Return all records belonging to the Template Modul Instanzen.
     *
     * @param string $iCmsTplModuleInstanceId - ID for the record in: Template Modul Instanzen
     * @param string $iLanguageId - set language id for list - if null, the default language will be used instead
     *
     * @return TdbCmsWizardStepList
     */
    public static function GetCmsWizardStepListForCmsTplModuleInstance($iCmsTplModuleInstanceId, $iLanguageId = null)
    {
        $oList = TAdbCmsWizardStepList::GetListForCmsTplModuleInstanceId($iCmsTplModuleInstanceId, $iLanguageId);
        $oList->iModuleInstanceId = $iCmsTplModuleInstanceId;

        return $oList;
    }

    /**
     * return the next step in line (null if there no other step).
     *
     * @param TdbCmsWizardStep $oStep
     *
     * @return TdbCmsWizardStep
     */
    public static function GetNextStep($oStep)
    {
        $oNextStep = null;
        $query = "SELECT * FROM `cms_wizard_step`
                WHERE `position` > '".MySqlLegacySupport::getInstance()->real_escape_string($oStep->fieldPosition)."'
                  AND `cms_tpl_module_instance_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($oStep->fieldCmsTplModuleInstanceId)."'
             ORDER BY `position`";
        $oSteps = TdbCmsWizardStepList::GetList($query);
        if ($oSteps->Length() > 0) {
            $oNextStep = $oSteps->Current();
        }

        return $oNextStep;
    }

    /**
     * return the previous step in line (null if there no other step).
     *
     * @param TdbCmsWizardStep $oStep
     *
     * @return TdbCmsWizardStep
     */
    public static function GetPreviousStep($oStep)
    {
        $oPreviousStep = null;
        $query = "SELECT *
                 FROM `cms_wizard_step`
                WHERE `position` < '".MySqlLegacySupport::getInstance()->real_escape_string($oStep->fieldPosition)."'
                  AND `cms_tpl_module_instance_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($oStep->fieldCmsTplModuleInstanceId)."'
             ORDER BY `position` DESC";
        $oSteps = TdbCmsWizardStepList::GetList($query);
        if ($oSteps->Length() > 0) {
            $oPreviousStep = $oSteps->Current();
        }

        return $oPreviousStep;
    }

    /**
     * return list of steps before the step passed.
     *
     * @param TdbCmsWizardStep $oStep
     *
     * @return TdbCmsWizardStepList
     */
    public static function GetAllBeforeThisStep($oStep)
    {
        $query = "SELECT *
                 FROM `cms_wizard_step`
                WHERE `position` < '".MySqlLegacySupport::getInstance()->real_escape_string($oStep->fieldPosition)."'
                  AND `cms_tpl_module_instance_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($oStep->fieldCmsTplModuleInstanceId)."'
             ORDER BY `position`";

        return TdbCmsWizardStepList::GetList($query);
    }

    /**
     * return the navi list for the current wizard.
     *
     * @param TdbCmsWizardStep $oActiveStep - current active step
     *
     * @return TdbCmsWizardStepList
     */
    public static function GetListNavigationForInstance(TdbCmsWizardStep $oActiveStep)
    {
        // $this->getf
        $oList = TdbCmsWizardStepList::GetCmsWizardStepListForCmsTplModuleInstance($oActiveStep->fieldCmsTplModuleInstanceId);
        $oList->GoToStart();
        $iStepCount = 0;
        $bActiveFound = false;
        while ($oItem = $oList->Next()) {
            ++$iStepCount;
            $oItem->iCurrentPositionInList = $iStepCount;
            if ($oItem->id == $oActiveStep->id) {
                $oItem->bIsActiveStep = true;
                $bActiveFound = true;
            } else {
                if ($bActiveFound) {
                    $oItem->bIsAfterActiveStep = true;
                } else {
                    $oItem->bIsBeforeActiveStep = true;
                }
            }
        }
        $oList->GoToStart();

        return $oList;
    }

    /**
     * render the step list.
     *
     * @param array $aCallTimeVars - place any custom vars that you want to pass through the call here
     *
     * @return string
     */
    public function Render($sViewName = 'standard', $sViewSubType = 'dbobjects', $sViewType = 'Customer', $sSpotName = null, $aCallTimeVars = [])
    {
        $oView = new TViewParser();

        $oWizardConf = TdbCmsWizardConfig::GetNewInstance();
        if (!$oWizardConf->LoadFromField('cms_tpl_module_instance_id', $this->iModuleInstanceId)) {
            $oWizardConf = null;
        }

        $oView->AddVar('oWizardConf', $oWizardConf);
        $oView->AddVar('oSteps', $this);

        $oView->AddVar('sSpotName', $sSpotName);
        $oView->AddVar('aCallTimeVars', $aCallTimeVars);

        $aOtherParameters = $this->GetAdditionalViewVariables($sViewName, $sViewType);
        $oView->AddVarArray($aOtherParameters);

        if ($oWizardConf->fieldListIsPackage) {
            $sHTML = $oView->RenderObjectPackageView($sViewName, $sViewSubType, $sViewType);
        } else {
            $sHTML = $oView->RenderObjectView($sViewName, $sViewSubType, $sViewType);
        }

        return $sHTML;
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
     * returns the position of the currently active step. if no step is marked as active,
     * it will return false. Step positions will start at 1 (not zero).
     *
     * @return int
     */
    public function GetActiveStepPosition()
    {
        $iActivePos = false;
        $iPointer = $this->getItemPointer();
        $this->GoToStart();
        $iStepNumber = 0;
        while ($oItem = $this->Next()) {
            ++$iStepNumber;
            if ($oItem->bIsActiveStep) {
                $iActivePos = $iStepNumber;
            }
        }
        $this->setItemPointer($iPointer);

        return $iActivePos;
    }
}
