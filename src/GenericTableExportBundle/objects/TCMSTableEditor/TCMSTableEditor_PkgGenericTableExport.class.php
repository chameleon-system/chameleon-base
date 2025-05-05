<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\CoreBundle\Util\UrlUtil;
use Symfony\Contracts\Translation\TranslatorInterface;

class TCMSTableEditor_PkgGenericTableExport extends TCMSTableEditor
{
    /**
     * adds table-specific buttons to the editor (add them directly to $this->oMenuItems).
     *
     * @return void
     */
    protected function GetCustomMenuItems()
    {
        parent::GetCustomMenuItems();

        $aParam = TGlobal::instance()->GetUserData(null, ['module_fnc', '_noModuleFunction']);
        $aParam['module_fnc'] = [TGlobal::instance()->GetExecutingModulePointer()->sModuleSpotName => 'RunExportToFileSystem'];
        $aParam['_noModuleFunction'] = 'true';

        $oMenuItem = new TCMSTableEditorMenuItem();
        $oMenuItem->sItemKey = 'runexporttofilesystem';
        $oMenuItem->setTitle($this->getTranslator()->trans('chameleon_system_generic_table_export.action.export_to_server'));
        $oMenuItem->sIcon = 'fas fa-file-export';
        $oMenuItem->href = '?'.$this->getUrlUtil()->getArrayAsUrl($aParam, '', '&');
        $this->oMenuItems->AddItem($oMenuItem);

        $aParam = TGlobal::instance()->GetUserData(null, ['module_fnc', '_noModuleFunction']);
        $aParam['module_fnc'] = [TGlobal::instance()->GetExecutingModulePointer()->sModuleSpotName => 'RunExportToDownload'];
        $aParam['_noModuleFunction'] = 'true';

        $oMenuItem = new TCMSTableEditorMenuItem();
        $oMenuItem->sItemKey = 'runexporttodownload';
        $oMenuItem->setTitle($this->getTranslator()->trans('chameleon_system_generic_table_export.action.export_and_download'));
        $oMenuItem->sIcon = 'fas fa-file-export';
        $oMenuItem->href = '?'.$this->getUrlUtil()->getArrayAsUrl($aParam, '', '&');
        $this->oMenuItems->AddItem($oMenuItem);
    }

    /**
     * @return void
     */
    public function DefineInterface()
    {
        parent::DefineInterface();
        $this->methodCallAllowed[] = 'RunExportToFileSystem';
        $this->methodCallAllowed[] = 'RunExportToDownload';
    }

    /**
     * @return bool
     */
    public function RunExportToFileSystem()
    {
        $oExport = TdbPkgGenericTableExport::GetNewInstance($this->oTable->sqlData);
        $sResult = $oExport->WriteExportToFile();

        return $sResult;
    }

    /**
     * @return never
     *
     * @psalm-suppress NoValue, InvalidReturnType - A method that never returns contains a return statement
     *
     * @FIXME `WriteExportToDownload` calls exit and never returns - saving its return or even returning at the end of this method value makes no sense.
     */
    public function RunExportToDownload()
    {
        $oExport = TdbPkgGenericTableExport::GetNewInstance($this->oTable->sqlData);
        $sResult = $oExport->WriteExportToDownload();

        return $sResult;
    }

    protected function getTranslator(): TranslatorInterface
    {
        return ServiceLocator::get('translator');
    }

    protected function getUrlUtil(): UrlUtil
    {
        return ServiceLocator::get('chameleon_system_core.util.url');
    }
}
