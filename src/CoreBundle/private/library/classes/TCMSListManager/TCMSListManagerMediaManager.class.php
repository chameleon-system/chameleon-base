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
 * extends the standard listing so that a preview image is shown, and if the
 * class is called with the right parameters it will show an assign button to
 * assign an image from the list to the calling record.
 * /**/
class TCMSListManagerMediaManager extends TCMSListManagerImagedatabase
{
    /**
     * we need this to overwrite the standard function column.
     */
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
        return 'parent.editFileDetails';
    }

    /**
     * add the preview image.
     */
    public function AddFields()
    {
        ++$this->columnCount;
        $this->tableObj->AddHeaderField(['id' => '#'], 'left', null, 1, false);
        $this->tableObj->AddColumn('id', 'left', [$this, 'CallBackMediaSelectBox'], null, 1);
        parent::AddFields();
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
