<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class CMSFieldMLTList extends TCMSModelBase
{
    /**
     * @var TdbCmsTblConf|null
     */
    public $oTableConf;

    /**
     * @var TCMSListManager|null
     */
    public $oTableList;

    /**
     * {@inheritdoc}
     */
    public function Init()
    {
        $this->oTableConf = TdbCmsTblConf::GetNewInstance($this->global->GetUserData('id'));

        // allow custom list class overwriting (defined in pagedef)
        $listClass = null;
        if (array_key_exists('listClass', $this->aModuleConfig)) {
            $listClass = $this->aModuleConfig['listClass'];
        }

        if (!is_null($listClass)) {
            $oTableList = $this->oTableConf->GetListObject($listClass);
            $this->data['sTable'] = $oTableList->GetList();
        } else {
            $sTableName = $this->global->GetUserData('name');
            $oTable = TdbCmsTblConf::GetNewInstance();
            if ($oTable->Load($this->global->GetUserData('id'))) {
                $sTableName = $oTable->fieldName;
            }
            $this->oTableList = TCMSTableConf::GetMLTListObject($sTableName);
            $this->oTableList->sRestriction = $this->global->GetUserData('sRestriction');
            $this->oTableList->sRestrictionField = $this->global->GetUserData('sRestrictionField');
            $this->oTableList->Init($this->oTableConf);
            $this->data['sTable'] = $this->oTableList->GetList();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function Execute()
    {
        $this->data = parent::Execute();

        $this->data['id'] = $this->global->GetUserData('id');
        $this->data['_isiniframe'] = $this->global->GetUserData('_isiniframe');
        $this->data['sRestriction'] = $this->global->GetUserData('sRestriction');
        $this->data['sRestrictionField'] = $this->global->GetUserData('sRestrictionField');

        $field = $this->global->GetUserData('field');
        $this->data['field'] = $field;
        $this->data['name'] = $this->global->GetUserData('name');

        return $this->data;
    }

    /**
     * {@inheritdoc}
     */
    public function GetHtmlHeadIncludes()
    {
        $includes = parent::GetHtmlHeadIncludes();
        // first the includes that are needed for the all fields
        $includes[] = '<link href="'.TGlobal::GetPathTheme().'/css/table.css" rel="stylesheet" type="text/css" />';
        $includes[] = '<link href="'.TGlobal::GetStaticURLToWebLib('/components/select2.v4/css/select2.min.css').'" media="screen" rel="stylesheet" type="text/css" />';

        return $includes;
    }

    public function GetHtmlFooterIncludes()
    {
        $includes = parent::GetHtmlFooterIncludes();
        $includes[] = '<script src="'.TGlobal::GetStaticURLToWebLib('/components/select2.v4/js/select2.full.min.js').'" type="text/javascript"></script>';
        $includes[] = '<script src="'.TGlobal::GetStaticURLToWebLib('/javascript/table.js').'" type="text/javascript"></script>';

        return $includes;
    }
}
