<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TCMSListManagerChangeLogItem extends TCMSListManagerFullGroupTable
{
    /**
     * add additional fields.
     */
    public function AddFields()
    {
        $jsParas = array('id');

        ++$this->columnCount;
        $this->tableObj->AddHeaderField(array('cms_field_conf' => TGlobal::Translate('chameleon_system_cms_change_log.column.changed_field')), 'left', null, 1, false);
        $this->tableObj->AddColumn('cms_field_conf', 'left', array($this, 'CallbackResolveFieldName'), $jsParas, 1);

        ++$this->columnCount;
        $this->tableObj->AddHeaderField(array('value_old' => TGlobal::Translate('chameleon_system_cms_change_log.column.old_value')), 'left', null, 1, false);
        $this->tableObj->AddColumn('value_old', 'left', array($this, 'CallbackResolveFieldValue'), $jsParas, 1);

        ++$this->columnCount;
        $this->tableObj->AddHeaderField(array('value_new' => TGlobal::Translate('chameleon_system_cms_change_log.column.new_value')), 'left', null, 1, false);
        $this->tableObj->AddColumn('value_new', 'left', array($this, 'CallbackResolveFieldValue'), $jsParas, 1);
    }

    /**
     * @param string $cellValue
     * @param array<string, mixed> $row
     * @param string $name
     * @return string
     */
    public function CallbackResolveFieldName($cellValue, $row, $name)
    {
        return TCMSChangeLogFormatter::formatFieldName($cellValue);
    }

    /**
     * @param string $cellValue
     * @param array<string, mixed> $row
     * @param string $name
     * @return string
     */
    public function CallbackResolveFieldValue($cellValue, $row, $name)
    {
        return TCMSChangeLogFormatter::formatFieldValue($row['cms_field_conf'], $cellValue);
    }
}
