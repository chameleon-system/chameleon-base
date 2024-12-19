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
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * holds a record from the "cms_tpl_module_instance" table.
/**/
class TCMSTPLModuleInstance extends TCMSRecord
{
    /**
     * module loader object.
     *
     * @var TModuleLoader
     */
    protected $oModuleLoader = null;

    /**
     * module record.
     *
     * @var TCMSTPLModule
     */
    protected $oModule = null;

    /**
     * the name of the spot the module instance is set.
     *
     * @var string
     */
    protected $sSpotName = null;

    public function __construct($id = null)
    {
        parent::__construct('cms_tpl_module_instance', $id);
    }

    /**
     * init module.
     *
     * @param string $sSpotName   - spot name
     * @param string $sView       - view to use
     * @param array  $aParameters - additional parameters
     */
    public function Init($sSpotName, $sView, $aParameters = array())
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
        if (is_null($this->oModule)) {
            $this->oModule = new TCMSTPLModule();
            $this->oModule->Load($this->sqlData['cms_tpl_module_id']);
        }

        return $this->oModule;
    }

    protected function GetModuleLoader($sView, $aParameters)
    {
        if (is_null($this->oModuleLoader)) {
            $aParameters['instanceID'] = $this->id;
            $this->GetModule();
            $this->oModuleLoader = TTools::GetModuleLoaderObject($this->oModule->sqlData['classname'], $sView, $aParameters, $this->sSpotName);
        }

        return $this->oModuleLoader;
    }

    /**
     * @return array
     */
    public function GetPermittedFunctions()
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
     * @param array  $aAdditionalModuleParameters
     * @param string $sModuleSpotName
     * @param string $sView
     *
     * @return string
     */
    public function Render($aAdditionalModuleParameters = array(), $sModuleSpotName = 'tmpmodule', $sView = '')
    {
        $dbAccessLayer = \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.database_access_layer_cms_tpl_module');
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
     *
     * @return string
     */
    public function GetModuleColorEditState()
    {
        $aModuleStateCodeColorList = array('ne' => 'EB3335', 'na' => 'FAA11C', 'ea' => '78C043');
        $sModuleState = $this->GetModuleEditState();

        return $aModuleStateCodeColorList[$sModuleState];
    }

    /**
     * Get the color for the edit state of the module connected table.
     *
     * @param TCMSRecord $oModuleConnectedTable
     *
     * @return string
     */
    public function GetModuleConnectedTableColorEditState($oModuleConnectedTable)
    {
        $aModuleStateCodeColorList = array('ne' => 'EB3335', 'na' => 'FAA11C', 'ea' => '78C043');
        $sModuleState = $this->UpdateModuleEditStateForConnectedTable('', $oModuleConnectedTable);

        return $aModuleStateCodeColorList[$sModuleState];
    }

    /**
     * Get the edit state of the module instances as code or human readable text.
     *
     * @param bool $bReturnTranslatedText to get the module instance edit state as human readable text
     *
     * @return string
     */
    public function GetModuleEditState($bReturnTranslatedText = false)
    {
        $translator = $this->getTranslator();
        $aModuleStateCodeList = array(
            'ne' => $translator->trans('chameleon_system_core.template_engine.module_edit_state_new', array(), TranslationConstants::DOMAIN_BACKEND),
            'na' => $translator->trans('chameleon_system_core.template_engine.module_edit_state_partial', array(), TranslationConstants::DOMAIN_BACKEND),
            'ea' => $translator->trans('chameleon_system_core.template_engine.module_edit_state_completed', array(), TranslationConstants::DOMAIN_BACKEND),
        );
        $sModuleState = 'na';
        $oInstanceModule = $this->GetFieldCmsTplModule();
        if (!is_object($oInstanceModule)) {
            if (!empty($this->fieldCmsTplModuleId)) {
                // something went terribly wrong! the connected module doesn`t exist anymore
                $sMessage = 'module with ID: '.$this->fieldCmsTplModuleId.' doesn´t exist anymore, but is set in instance: '.$this->id.' - '.$this->fieldName;
                TTools::WriteLogEntry($sMessage, 1, __FILE__, __LINE__);
            }
        } else {
            $oModuleConnectedTableList = $oInstanceModule->GetMLT('cms_tbl_conf_mlt');
            if ($oModuleConnectedTableList->Length() > 0) {
                while ($oModuleConnectedTable = $oModuleConnectedTableList->Next()) {
                    $sModuleState = $this->UpdateModuleEditStateForConnectedTable($sModuleState, $oModuleConnectedTable);
                }
            } else {
                $sModuleState = 'ea';
            }
            if ($bReturnTranslatedText) {
                $sModuleState = $aModuleStateCodeList[$sModuleState];
            }
        }

        return $sModuleState;
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
        $oRecordList = call_user_func_array(array($sClassName, 'GetList'), array($Query, null, false, true, true));
        if (is_object($oRecordList) && $oRecordList->Length() > 0) {
            $HasConnectedTableInstanceRecord = true;
        }

        return $HasConnectedTableInstanceRecord;
    }

    /**
     * Update the module instance edit state for given module connected table.
     *
     * @param string     $sModuleState
     * @param TCMSRecord $oModuleConnectedTable
     *
     * @return string
     */
    protected function UpdateModuleEditStateForConnectedTable($sModuleState, $oModuleConnectedTable)
    {
        $bFoundModuleInstanceField = false;
        $sFieldClassName = TCMSTableToClass::GetClassName(TCMSTableToClass::PREFIX_CLASS, 'cms_field_conf');
        $oFieldList = $oModuleConnectedTable->GetProperties('cms_field_conf_mlt', $sFieldClassName);
        while ($oField = $oFieldList->Next()) {
            if ('cms_tpl_module_instance_id' == $oField->fieldName) {
                $bFoundModuleInstanceField = true;
                if (!$this->HasConnectedTableInstanceRecord($oModuleConnectedTable)) {
                    if ('ea' == $sModuleState) {
                        $sModuleState = 'na';
                    } elseif ('' == $sModuleState) {
                        $sModuleState = 'ne';
                    }
                } else {
                    if ('' == $sModuleState) {
                        $sModuleState = 'ea';
                    } elseif ('ne' == $sModuleState) {
                        $sModuleState = 'na';
                    }
                }
            }
        }
        if (!$bFoundModuleInstanceField) {
            if ('ne' == $sModuleState) {
                $sModuleState = 'na';
            } elseif ('' == $sModuleState) {
                $sModuleState = 'ea';
            }
        }

        return $sModuleState;
    }

    /**
     * @return TranslatorInterface
     */
    private function getTranslator()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('translator');
    }
}
