<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\Exception\MaintenanceModeErrorException;
use ChameleonSystem\CoreBundle\Interfaces\FlashMessageServiceInterface;
use ChameleonSystem\CoreBundle\Maintenance\MaintenanceMode\MaintenanceModeServiceInterface;
use ChameleonSystem\CoreBundle\ServiceLocator;

class TCMSTableEditorCMSConfig extends TCMSTableEditor
{
    /**
     * set public methods here that may be called from outside.
     */
    public function DefineInterface()
    {
        parent::DefineInterface();
        $this->methodCallAllowed[] = 'UpdateTranslationFields';
    }

    /**
     * add table-specific buttons to the editor (add them directly to $this->oMenuItems).
     */
    protected function GetCustomMenuItems()
    {
        parent::GetCustomMenuItems();
        $oMenuItem = new TCMSTableEditorMenuItem();
        $oMenuItem->sItemKey = 'updateTranslationFields';
        $oMenuItem->sDisplayName = ServiceLocator::get('translator')->trans('chameleon_system_core.table_editor.regenerate_translatable_fields');
        $oMenuItem->sIcon = 'fas fa-font';

        $sCallURL = PATH_CMS_CONTROLLER.'?'.TTools::GetArrayAsURLForJavascript(['pagedef' => 'tableeditor', 'id' => $this->sId, 'tableid' => $this->oTableConf->id, 'module_fnc' => ['contentmodule' => 'UpdateTranslationFields'], '_noModuleFunction' => 'true']);

        $oMenuItem->sOnClick = "if (confirm('".TGlobal::OutJS(ServiceLocator::get('translator')->trans('chameleon_system_core.table_editor.regenerate_translatable_fields_confirm'))."')) document.location.href='{$sCallURL}';";
        $this->oMenuItems->AddItem($oMenuItem);
    }

    /**
     * refresh the translation fields of all tables.
     */
    public function UpdateTranslationFields()
    {
        // get language list
        $oConfig = TdbCmsConfig::GetInstance(true);
        /** @var $oConfig TdbCmsConfig */
        if (!empty($oConfig->sqlData['translation_base_language_id'])) {
            // ok, we have a list of "other" languages (in addition to the base language)
            $sQuery = "SELECT `cms_field_conf`.*
                   FROM `cms_field_conf`
                  WHERE `cms_field_conf`.`is_translatable` = '1'
                ";
            $oDefList = TdbCmsFieldConfList::GetList($sQuery);
            $oDefList->GoToStart();
            while ($oDef = $oDefList->Next()) {
                $oDef->UpdateFieldTranslationKeys();
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function PostSaveHook($oFields, $oPostTable)
    {
        parent::PostSaveHook($oFields, $oPostTable);

        $maintenanceModeService = $this->getMaintenanceModeService();

        /**
         * @var TdbCmsConfig $newConfig
         */
        $newConfig = $this->oTable;

        try {
            if (true === $newConfig->fieldShutdownWebsites) {
                $maintenanceModeService->activate();
            } else {
                $maintenanceModeService->deactivate();
            }
        } catch (MaintenanceModeErrorException $exception) {
            $flashMessageService = $this->getFlashMessageService();

            $flashMessageService->addMessage(
                TCMSTableEditorManager::MESSAGE_MANAGER_CONSUMER,
                'TABLEEDITOR_CMS_CONFIG_MAINTENANCE_ERROR',
                ['exceptionMessage' => $exception->getMessage()]
            );
        }
    }

    private function getMaintenanceModeService(): MaintenanceModeServiceInterface
    {
        return ServiceLocator::get('chameleon_system_core.maintenance_mode_service');
    }

    private function getFlashMessageService(): FlashMessageServiceInterface
    {
        return ServiceLocator::get('chameleon_system_core.flash_messages');
    }
}
