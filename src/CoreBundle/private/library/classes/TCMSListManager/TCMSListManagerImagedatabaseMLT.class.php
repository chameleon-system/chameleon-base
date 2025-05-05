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
use Doctrine\DBAL\Connection;

/**
 * extends the standard listing so that a preview image is shown, and if the
 * class is called with the right parameters it will show an assign button to
 * assign an image from the list to the calling record.
 * /**/
class TCMSListManagerImagedatabaseMLT extends TCMSListManagerMLT
{
    /**
     * add the preview image.
     */
    public function AddFields()
    {
        $jsParas = ['id'];
        ++$this->columnCount;
        $sTranslatedField = ServiceLocator::get('translator')->trans('chameleon_system_core.list_image_database.column_name_preview');
        $this->tableObj->AddHeaderField(['path' => $sTranslatedField], 'left', null, 1, false);
        $this->tableObj->AddColumn('path', 'left', [$this, 'CallBackImageWithZoom'], $jsParas, 1);
        parent::AddFields();
    }

    /**
     * {@inheritDoc}
     */
    public function GetCustomRestriction()
    {
        $query = parent::GetCustomRestriction();

        if ('' !== $query) {
            $query .= ' AND ';
        }

        $dbConnection = $this->getDatabaseConnection();
        $query .= $dbConnection->quoteIdentifier($this->oTableConf->sqlData['name']).'.`cmsident` >= 1000';

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

    private function getDatabaseConnection(): Connection
    {
        return ServiceLocator::get('database_connection');
    }
}
