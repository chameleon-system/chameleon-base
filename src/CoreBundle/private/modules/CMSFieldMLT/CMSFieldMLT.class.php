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
 * the CMS MLT Field.
/**/
class CMSFieldMLT extends TCMSModelBase
{
    /**
     * table conf.
     *
     * @var TCMSTableConf
     */
    public $oTableConf = null;

    /**
     * called before the constructor, and before any external functions get called, but
     * after the constructor.
     */
    public function Init()
    {
        /** @var $oTable TCMSTableConf */
        $this->oTableConf = new TCMSTableConf();
        $this->oTableConf->Load($this->global->GetUserData('id'));
        $this->data['sTableName'] = $this->oTableConf->sqlData['name'];
    }

    public function &Execute()
    {
        $this->data = parent::Execute();

        $oTableList = &$this->oTableConf->GetListObject();
        $this->data['sTable'] = $oTableList->GetList();
        $this->data['id'] = $this->global->GetUserData('id');
        $this->data['sRestriction'] = $this->global->GetUserData('sRestriction');
        $this->data['sRestrictionField'] = $this->global->GetUserData('sRestrictionField');
        $this->data['_isiniframe'] = $this->global->GetUserData('_isiniframe');
        $this->data['field'] = $this->global->GetUserData('field');
        $this->data['name'] = $this->global->GetUserData('name');

        return $this->data;
    }

    public function GetHtmlHeadIncludes()
    {
        $aIncludes = parent::GetHtmlHeadIncludes();
        // first the includes that are needed for the all fields
        $aIncludes[] = '<link href="'.TGlobal::GetPathTheme().'/css/table.css" rel="stylesheet" type="text/css" />';
        $aIncludes[] = '<script src="'.TGlobal::GetStaticURLToWebLib('/javascript/table.js').'" type="text/javascript"></script>';

        return $aIncludes;
    }
}
