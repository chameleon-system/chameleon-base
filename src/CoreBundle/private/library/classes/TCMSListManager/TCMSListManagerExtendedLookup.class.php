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
 * uses the TFullGroupTable to manage the list.
 * /**/
class TCMSListManagerExtendedLookup extends TCMSListManagerFullGroupTable
{
    public function _AddFunctionColumn()
    {
    }

    /**
     * returns the name of the javascript function to be called when the user clicks on a
     * record within the table.
     *
     * @return string
     */
    public function _GetRecordClickJavaScriptFunctionName()
    {
        $sReturnValue = 'selectExtendedLookupRecord';
        $oGlobal = TGlobal::instance();
        if ($oGlobal->UserDataExists('fieldName')) {
            $oCmsFieldConf = TdbCmsFieldConf::GetNewInstance();
            $oCmsFieldConf->LoadFromFields(['name' => $oGlobal->GetUserData('fieldName'), 'cms_tbl_conf_id' => $oGlobal->GetUserData('sourceTblConfId')]);

            $oCmsFieldType = $oCmsFieldConf->GetFieldCmsFieldType();
            if ('CMSFIELD_EXTENDEDMULTITABLELIST' == $oCmsFieldType->fieldConstname) {
                $sReturnValue = 'selectExtendedLookupMultiTableRecord';
            }
        }

        return $sReturnValue;
    }

    /**
     * by returning false the "new entry" button in the list can be supressed.
     *
     * @return bool
     */
    public function ShowNewEntryButton()
    {
        return true;
    }

    protected function AddRowPrefixFields()
    {
    }

    /**
     * add table-specific buttons to the editor (add them directly to $this->oMenuItems).
     */
    protected function GetCustomMenuItems()
    {
        parent::GetCustomMenuItems();
        $this->oMenuItems->RemoveItem('sItemKey', 'deleteall');
        $this->oMenuItems->RemoveItem('sItemKey', 'edittableconf');
    }
}
