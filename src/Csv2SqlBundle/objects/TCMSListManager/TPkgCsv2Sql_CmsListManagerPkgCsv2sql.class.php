<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TPkgCsv2Sql_CmsListManagerPkgCsv2sql extends TCMSListManagerFullGroupTable
{
    /**
     * set public methods here that may be called from outside.
     *
     * @return void
     */
    public function DefineInterface()
    {
        parent::DefineInterface();
        $this->methodCallAllowed[] = 'ProcessImport';
    }

    /**
     * add table-specific buttons to the editor (add them directly to $this->oMenuItems).
     *
     * @return void
     */
    protected function GetCustomMenuItems()
    {
        parent::GetCustomMenuItems();
        $oMenuItem = new TCMSTableEditorMenuItem();
        $oMenuItem->sItemKey = 'ProcessImport';
        $oMenuItem->sDisplayName = TGlobal::Translate('chameleon_system_csv2sql.action.run_import');
        $oMenuItem->sIcon = 'fas fa-upload';

        $aCallParams = array('pagedef' => 'tablemanager', //tableeditor
            //'id'=>$this->sId,
            //'tableid'=>$this->oTableConf->id,
            'id' => $this->oTableConf->id, 'module_fnc' => array('contentmodule' => 'ExecuteAjaxCall'), '_fnc' => 'ProcessImport', //'_noModuleFunction'=>'true' // use for TCMSTableEditor!
            'callListManagerMethod' => true, );
        $sCallURL = PATH_CMS_CONTROLLER.'?'.TTools::GetArrayAsURLForJavascript($aCallParams);
        $oMenuItem->sOnClick = "GetAjaxCall('{$sCallURL}', TPkgCsv2SqlShowInfo);";
        $this->oMenuItems->AddItem($oMenuItem);
    }

    /**
     * @return string
     */
    public function ProcessImport()
    {
        /** @var $oView TViewParser */
        $oView = new TViewParser();
        $aData = TPkgCsv2SqlManager::ProcessAll();
        $oView->AddVarArray($aData);

        return $oView->RenderObjectPackageView('vResult', 'pkgCsv2Sql/views/TCMSListManager/TPkgCsv2Sql_CmsListManagerPkgCsv2sql', 'Customer');
    }

    public function GetHtmlHeadIncludes()
    {
        $aIncludes = parent::GetHtmlHeadIncludes();
        $aIncludes[] = "<script type=\"text/javascript\">
      function TPkgCsv2SqlShowInfo(data,statusText) {
        CloseModalIFrameDialog();
        CreateModalIFrameDialogFromContent(data,0,0,'Result');
      }
      </script>";
        $aIncludes[] = '<link href="'.TGlobal::GetStaticURL('/static2/TPkgCsv2Sql/markup.css').'" media="screen" rel="stylesheet" type="text/css" />';

        return $aIncludes;
    }

    /**
     * here you can add checks to validate the data and prevent saving.
     *
     * @param array     $postData - raw post data (e.g. datetime fields are splitted into 2 post values and in non sql format)
     * @param TIterator $oFields - TIterator of TCMSField objects
     *
     * @return bool
     */
    protected function DataIsValid(&$postData, $oFields = null)
    {
        return true;
    }
}
