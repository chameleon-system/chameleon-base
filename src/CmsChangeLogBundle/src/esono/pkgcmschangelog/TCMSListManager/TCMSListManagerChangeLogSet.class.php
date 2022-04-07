<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TCMSListManagerChangeLogSet extends TCMSListManagerFullGroupTable
{
    /**
     * add additional fields.
     *
     * @return void
     */
    public function AddFields()
    {
        $jsParas = array('id');

        ++$this->columnCount;
        $this->tableObj->AddHeaderField(array('cms_tbl_conf' => TGlobal::Translate('chameleon_system_cms_change_log.column.changed_table')), 'left', null, 1, false);
        $this->tableObj->AddColumn('cms_tbl_conf', 'left', array($this, 'CallbackResolveTableName'), $jsParas, 1);

        ++$this->columnCount;
        $this->tableObj->AddHeaderField(array('modified_name' => TGlobal::Translate('chameleon_system_cms_change_log.column.changed_record')), 'left', null, 1, false);
        $this->tableObj->AddColumn('modified_name', 'left', null, $jsParas, 1);
        $this->tableObj->searchFields['`pkg_cms_changelog_set`.`modified_name`'] = 'full'; // allow searching in this field

        ++$this->columnCount;
        $this->tableObj->AddHeaderField(array('modify_date' => TGlobal::Translate('chameleon_system_cms_change_log.column.changed_on')), 'left', null, 1, false);
        $this->tableObj->AddColumn('modify_date', 'left', array($this, 'CallbackFormatDate'), $jsParas, 1);

        ++$this->columnCount;
        $this->tableObj->AddHeaderField(array('cms_user' => TGlobal::Translate('chameleon_system_cms_change_log.column.changed_by')), 'left', null, 1, false);
        $this->tableObj->AddColumn('cms_user', 'left', array($this, 'CallbackFormatUser'), $jsParas, 1);
        $this->tableObj->searchFields['`pkg_cms_changelog_set`.`cms_user`'] = 'full'; // allow searching in this field

        ++$this->columnCount;
        $this->tableObj->AddHeaderField(array('change_type' => TGlobal::Translate('chameleon_system_cms_change_log.column.change_type')), 'left', null, 1, false);
        $this->tableObj->AddColumn('change_type', 'left', array($this, 'CallbackFormatChangeType'), $jsParas, 1);
    }

    /**
     * @param string $cellValue
     * @param array<string, mixed> $row
     * @param string $name
     * @return string
     */
    public function CallbackResolveTableName($cellValue, $row, $name)
    {
        return TCMSChangeLogFormatter::formatTableName($cellValue);
    }

    /**
     * @param string $cellValue
     * @param array<string, mixed> $row
     * @param string $name
     * @return string
     */
    public function CallbackFormatDate($cellValue, $row, $name)
    {
        return TCMSChangeLogFormatter::formatDateTime($cellValue);
    }

    /**
     * @param string $cellValue
     * @param array<string, mixed> $row
     * @param string $name
     * @return string
     */
    public function CallbackFormatUser($cellValue, $row, $name)
    {
        return TCMSChangeLogFormatter::formatUser($cellValue);
    }

    /**
     * @param string $cellValue
     * @param array<string, mixed> $row
     * @param string $name
     * @return string
     */
    public function CallbackFormatChangeType($cellValue, $row, $name)
    {
        return TCMSChangeLogFormatter::formatChangeType($cellValue);
    }
}
