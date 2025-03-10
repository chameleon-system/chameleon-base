<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\Exception\ModuleException;
use ChameleonSystem\CoreBundle\i18n\TranslationConstants;
use ChameleonSystem\CoreBundle\ServiceLocator;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * holds a record from the "cms_tpl_module_instance" table.
 * /**/
class TCMSTPLModuleInstance extends TCMSRecord
{
    /**
     * module loader object.
     */
    protected ?TModuleLoader $oModuleLoader = null;

    /**
     * module record.
     */
    protected TCMSTPLModule $oModule;

    /**
     * the name of the spot the module instance is set.
     */
    protected string $sSpotName;

    /** @var string Module state: No edits made yet */
    public const STATE_NEW = 'new';

    /** @var string Module state: Partially edited (only some related tables edited) */
    public const STATE_PARTIAL = 'partial';

    /** @var string Module state: Fully edited (all related tables edited) */
    public const STATE_COMPLETED = 'completed';

    public array $moduleStateCodeColorList = [
        self::STATE_NEW => 'F86C6B', // danger
        self::STATE_PARTIAL => 'FFC107', // warning
        self::STATE_COMPLETED => '', // all edited
    ];

    public function __construct($id = null)
    {
        parent::__construct('cms_tpl_module_instance', $id);
    }

    /**
     * init module.
     *
     * @param string $sSpotName - spot name
     * @param string $sView - view to use
     * @param array $aParameters - additional parameters
     */
    public function Init($sSpotName, $sView, $aParameters = [])
    {
        $this->sSpotName = $sSpotName;
        $this->GetModuleLoader($sView, $aParameters);
        $this->oModuleLoader->InitModules();
    }

    /**
     * execute public method on module.
     *
     * @param string $sMethodName
     */
    public function ExecuteMethod($sMethodName)
    {
        $this->oModuleLoader->CallPublicModuleFunction($this->sSpotName, $sMethodName);
    }

    /**
     * renders the module specific sub navigation.
     *
     * @return string - html: ul,li a construct
     */
    public function RenderNavigation()
    {
        $sNavi = '';
        $this->oModuleLoader->CallPublicModuleFunction($this->sSpotName, 'GenerateModuleNavigation');
        $oModule = $this->oModuleLoader->GetPointerToModule($this->sSpotName);
        if (array_key_exists('sModuleNavigation', $oModule->data)) {
            $sNavi = $oModule->data['sModuleNavigation'];
        }

        return $sNavi;
    }

    /**
     * Load module object not loaded.
     *
     * @return TCMSTPLModule
     */
    protected function GetModule()
    {
        if (null === $this->oModule) {
            $this->oModule = new TCMSTPLModule();
            $this->oModule->Load($this->sqlData['cms_tpl_module_id']);
        }

        return $this->oModule;
    }

    protected function GetModuleLoader($sView, $aParameters)
    {
        if (null === $this->oModuleLoader) {
            $aParameters['instanceID'] = $this->id;
            $this->GetModule();
            $this->oModuleLoader = TTools::GetModuleLoaderObject($this->oModule->sqlData['classname'], $sView, $aParameters, $this->sSpotName);
        }

        return $this->oModuleLoader;
    }

    public function GetPermittedFunctions(): array
    {
        return $this->oModuleLoader->GetPermittedFunctions();
    }

    public function GetHtmlHeadIncludes()
    {
        return array_unique($this->oModuleLoader->GetHtmlHeadIncludes());
    }

    /**
     * new render version. renders the module and returns string.
     *
     * @return string
     *
     * @throws ModuleException
     */
    public function RenderModule()
    {
        return $this->oModuleLoader->GetModule($this->sSpotName, true);
    }

    /**
     * renders the module for the actual instance and returns a html string.
     *
     * @param array $aAdditionalModuleParameters
     * @param string $sModuleSpotName
     * @param string $sView
     *
     * @return string
     */
    public function Render($aAdditionalModuleParameters = [], $sModuleSpotName = 'tmpmodule', $sView = '')
    {
        $dbAccessLayer = ServiceLocator::get('chameleon_system_core.database_access_layer_cms_tpl_module');
        $oModule = $dbAccessLayer->loadFromId($this->sqlData['cms_tpl_module_id']);

        // now create instance of TModuleLoader...
        $aAdditionalModuleParameters['instanceID'] = $this->id;
        if (empty($sView)) {
            $sView = $this->sqlData['template'];
        } // if $sView is empty use last used view for the module instance

        return TTools::CallModule($oModule->sqlData['classname'], $sView, $aAdditionalModuleParameters, $sModuleSpotName);
    }

    /**
     * returns the first found template page object where this module instance is used.
     *
     * @return TdbCmsTplPage|null
     */
    public function GetConnectedPage()
    {
        $oPage = $this->GetFromInternalCache('oConnectedPage');
        if (is_null($oPage)) {
            $query = "SELECT `cms_tpl_page`.*
                    FROM `cms_tpl_page`
              INNER JOIN `cms_tpl_page_cms_master_pagedef_spot` ON `cms_tpl_page`.`id` = `cms_tpl_page_cms_master_pagedef_spot`.`cms_tpl_page_id`
                   WHERE `cms_tpl_page_cms_master_pagedef_spot`.`cms_tpl_module_instance_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->id)."'
                   LIMIT 0,1
                 ";
            if ($aPage = MySqlLegacySupport::getInstance()->fetch_assoc(MySqlLegacySupport::getInstance()->query($query))) {
                $oPage = TdbCmsTplPage::GetNewInstance();
                $oPage->LoadFromRow($aPage);
                $this->SetInternalCache('oConnectedPage', $oPage);
            }
        }

        return $oPage;
    }

    /**
     * Get the color for the edit state of the module instance.
     */
    public function GetModuleColorEditState(): string
    {
        $moduleState = $this->GetModuleEditState();

        return $this->moduleStateCodeColorList[$moduleState];
    }

    /**
     * Get the color for the edit state of the module connected table.
     */
    public function GetModuleConnectedTableColorEditState(string $moduleConnectedTableId): string
    {
        $moduleState = $this->UpdateModuleEditStateForConnectedTable(self::STATE_NEW, $moduleConnectedTableId);

        return $this->moduleStateCodeColorList[$moduleState];
    }

    /**
     * Get the edit state of the module instances as code or human-readable text.
     */
    public function GetModuleEditState(): string
    {
        $moduleState = self::STATE_PARTIAL;
        $oInstanceModule = $this->GetFieldCmsTplModule();

        if (null === $oInstanceModule) {
            $this->logMissingModule();

            return $moduleState;
        }

        return $this->determineModuleState($oInstanceModule, $moduleState);
    }

    /**
     * @todo not used at the moment, should be handled in the template
     */
    public function getModuleEditStateTranslation(): string
    {
        $translator = $this->getTranslator();

        $moduleStateCodeList = [
            self::STATE_NEW => $translator->trans('chameleon_system_core.template_engine.module_edit_state_new', [], TranslationConstants::DOMAIN_BACKEND),
            self::STATE_PARTIAL => $translator->trans('chameleon_system_core.template_engine.module_edit_state_partial', [], TranslationConstants::DOMAIN_BACKEND),
            self::STATE_COMPLETED => $translator->trans('chameleon_system_core.template_engine.module_edit_state_completed', [], TranslationConstants::DOMAIN_BACKEND),
        ];

        $moduleState = $this->GetModuleEditState();

        return $moduleStateCodeList[$moduleState];
    }

    private function logMissingModule(): void
    {
        if (!empty($this->fieldCmsTplModuleId)) {
            $message = 'module with ID: '.$this->fieldCmsTplModuleId.' doesn´t exist anymore, but is set in instance: '.$this->id.' - '.$this->fieldName;
            $this->getLogger()->error($message);
        }
    }

    private function determineModuleState(TdbCmsTplModule $instanceModule, string $moduleState): string
    {
        $moduleConnectedTableList = $instanceModule->GetMLT('cms_tbl_conf_mlt');

        if (null === $moduleConnectedTableList) {
            return self::STATE_COMPLETED;
        }

        if ($moduleConnectedTableList->Length() > 0) {
            while ($oModuleConnectedTable = $moduleConnectedTableList->Next()) {
                $moduleState = $this->UpdateModuleEditStateForConnectedTable($moduleState, $oModuleConnectedTable->id);
            }
        } else {
            $moduleState = self::STATE_COMPLETED;
        }

        return $moduleState;
    }

    /**
     * Check if the module connected table has record with actual module instance.
     *
     * @param TCMSRecord $oConnectedTableConf
     *
     * @return bool
     */
    protected function HasConnectedTableInstanceRecord($oConnectedTableConf)
    {
        $HasConnectedTableInstanceRecord = false;
        $sClassName = TCMSTableToClass::GetClassName(TCMSTableToClass::PREFIX_CLASS, $oConnectedTableConf->sqlData['name']).'List';
        $Query = 'SELECT * FROM `'.MySqlLegacySupport::getInstance()->real_escape_string($oConnectedTableConf->sqlData['name']).'`
                        WHERE `'.MySqlLegacySupport::getInstance()->real_escape_string($oConnectedTableConf->sqlData['name'])."`.`cms_tpl_module_instance_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->id)."'";
        $oRecordList = call_user_func_array([$sClassName, 'GetList'], [$Query, null, false, true, true]);
        if (is_object($oRecordList) && $oRecordList->Length() > 0) {
            $HasConnectedTableInstanceRecord = true;
        }

        return $HasConnectedTableInstanceRecord;
    }

    /**
     * Updates the module instance edit state for the given connected table.
     *
     * @param string $moduleState the initial module state
     * @param string $moduleConnectedTableId the connected table record id
     *
     * @return string updated module state
     */
    protected function UpdateModuleEditStateForConnectedTable(string $moduleState, string $moduleConnectedTableId): string
    {
        $foundModuleInstanceField = false;
        $fieldClassName = TCMSTableToClass::GetClassName(TCMSTableToClass::PREFIX_CLASS, 'cms_field_conf');
        $moduleConnectedTable = new TCMSRecord('cms_tbl_conf', $moduleConnectedTableId);
        $fieldList = $moduleConnectedTable->GetProperties('cms_field_conf_mlt', $fieldClassName);

        if (null === $fieldList) {
            return self::STATE_COMPLETED;
        }

        while ($oField = $fieldList->Next()) {
            if ('cms_tpl_module_instance_id' === $oField->fieldName) {
                $foundModuleInstanceField = true;
                if (false === $this->HasConnectedTableInstanceRecord($moduleConnectedTable)) {
                    if (self::STATE_COMPLETED === $moduleState) {
                        $moduleState = self::STATE_PARTIAL;
                    } elseif ('' === $moduleState) {
                        $moduleState = self::STATE_NEW;
                    }
                } elseif (self::STATE_PARTIAL === $moduleState) {
                    $moduleState = self::STATE_COMPLETED;
                } elseif (self::STATE_NEW === $moduleState) {
                    $moduleState = self::STATE_PARTIAL;
                }
            }
        }

        if (!$foundModuleInstanceField) {
            if (self::STATE_NEW === $moduleState) {
                $moduleState = self::STATE_PARTIAL;
            } elseif ('' === $moduleState) {
                $moduleState = self::STATE_COMPLETED;
            }
        }

        return $moduleState;
    }

    private function getTranslator(): TranslatorInterface
    {
        return ServiceLocator::get('translator');
    }

    private function getLogger(): LoggerInterface
    {
        return ServiceLocator::get('logger');
    }
}
