<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class CMSModuleDocumentChooser extends TModelBase
{
    public function Execute()
    {
        $this->data = parent::Execute();

        $this->_LoadDocumentLibrary();

        return $this->data;
    }

    /**
     * load the list object for existing module instances so the user can choose
     * one from the list and place it into a slot.
     */
    protected function _LoadDocumentLibrary()
    {
        // need to pass the parameters (modulespotname) back to the view
        $oListTable = new TCMSListManagerDocumentChooser();
        /** @var $oListTable TCMSListManagerMediaSelector */
        if ($this->global->UserDataExists('sRestriction')) {
            $oListTable->sRestriction = $this->global->GetUserData('sRestriction');
        }

        $oTableConf = new TCMSTableConf();
        /** @var $oTableConf TCMSTableConf */
        $oTableConf->LoadFromField('name', 'cms_document');

        $oListTable->Init($oTableConf);
        $this->data['sTable'] = $oListTable->GetList();
    }

    public function GetHtmlHeadIncludes()
    {
        $aIncludes = parent::GetHtmlHeadIncludes();
        // first the includes that are needed for the all fields
        $aIncludes[] = '<script src="'.TGlobal::GetStaticURLToWebLib('/javascript/cms.v2.js').'" type="text/javascript"></script>';
        $aIncludes[] = '<link href="'.TGlobal::GetPathTheme().'/css/table.css" rel="stylesheet" type="text/css" />';
        $aIncludes[] = '<script src="'.TGlobal::GetStaticURLToWebLib('/javascript/table.js').'" type="text/javascript"></script>';

        return $aIncludes;
    }
}
