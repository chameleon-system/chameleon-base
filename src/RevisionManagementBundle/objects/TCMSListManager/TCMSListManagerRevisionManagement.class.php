<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TCMSListManagerRevisionManagement extends TCMSListManagerFullGroupTable
{
    /**
     * add table-specific buttons to the editor (add them directly to $this->oMenuItems).
     */
    protected function GetCustomMenuItems()
    {
        parent::GetCustomMenuItems();
        $this->oMenuItems->RemoveItem('sItemKey', 'new');
        // $this->oMenuItems->RemoveItem('sItemKey','edittableconf');
    }

    public function _AddFunctionColumn()
    {
    }

    /**
     * any custom restrictions can be added to the query by overwriting this function.
     */
    public function GetCustomRestriction()
    {
        $query = parent::GetCustomRestriction();

        $oGlobal = TGlobal::instance();
        $sTableId = $oGlobal->GetUserData('sTableId');
        if (empty($sTableId)) {
            /**
             * the table id is mandatory and we don`t want to show the list if it is missing, so
             * set default non existent id.
             */
            $sTableId = 'xxx';
        }

        $query .= ' AND '.$this->CreateRestriction('cms_tbl_conf_id', "= '".MySqlLegacySupport::getInstance()->real_escape_string($sTableId)."'");
        // $query .= " AND `cms_record_revision`.`cms_record_revision_id` = ''";

        return $query;
    }

    /**
     * returns the name of the javascript function to be called when the user clicks on a
     * record within the table.
     *
     * @return string
     */
    protected function _GetRecordClickJavaScriptFunctionName()
    {
        return 'parent.ActivateRecordRevision';
    }
}
