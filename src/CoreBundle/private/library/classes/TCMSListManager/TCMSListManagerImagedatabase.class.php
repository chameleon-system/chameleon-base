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
/**/
class TCMSListManagerImagedatabase extends TCMSListManagerFullGroupTable
{
    /**
     * add the preview image.
     */
    public function AddFields()
    {
        $jsParas = array('id');
        ++$this->columnCount;
        $sTranslatedField = TGlobal::Translate('chameleon_system_core.list_image_database.column_name_preview');
        $this->tableObj->AddHeaderField(array('path' => $sTranslatedField), 'left', null, 1, false);
        $this->tableObj->AddColumn('path', 'left', array($this, 'CallBackImageWithZoom'), $jsParas, 1);
        parent::AddFields();
    }

    /**
     * restrict the list to show only images starting at cmsident > 1000.
     *
     * @return string $query
     */
    public function GetCustomRestriction()
    {
        $query = '';
        $query .= parent::GetCustomRestriction();
        if (!empty($query)) {
            $query .= ' AND ';
        }
        $query .= '`'.MySqlLegacySupport::getInstance()->real_escape_string($this->oTableConf->sqlData['name']).'`.`cmsident` >= 1000 AND `'.MySqlLegacySupport::getInstance()->real_escape_string($this->oTableConf->sqlData['name'])."`.`id` != '-1'";

        return $query;
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

    /**
     * {@inheritdoc}
     */
    protected function usesManagedTables(): bool
    {
        return false;
    }
}
