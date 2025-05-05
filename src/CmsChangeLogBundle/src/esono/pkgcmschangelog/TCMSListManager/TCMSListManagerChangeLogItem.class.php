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
     *
     * @return void
     */
    public function AddFields()
    {
        $jsParas = ['id'];

        ++$this->columnCount;
        $this->tableObj->AddHeaderField(['cms_field_conf' => ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_cms_change_log.column.changed_field')], 'left', null, 1, false);
        $this->tableObj->AddColumn('cms_field_conf', 'left', [$this, 'CallbackResolveFieldName'], $jsParas, 1);

        ++$this->columnCount;
        $this->tableObj->AddHeaderField(['value_old' => ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_cms_change_log.column.old_value')], 'left', null, 1, false);
        $this->tableObj->AddColumn('value_old', 'left', [$this, 'CallbackResolveFieldValue'], $jsParas, 1);

        ++$this->columnCount;
        $this->tableObj->AddHeaderField(['value_new' => ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_cms_change_log.column.new_value')], 'left', null, 1, false);
        $this->tableObj->AddColumn('value_new', 'left', [$this, 'CallbackResolveFieldValue'], $jsParas, 1);
    }

    /**
     * @param string $cellValue
     * @param array<string, mixed> $row
     * @param string $name
     *
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
     *
     * @return string
     */
    public function CallbackResolveFieldValue($cellValue, $row, $name)
    {
        return TCMSChangeLogFormatter::formatFieldValue($row['cms_field_conf'], $cellValue);
    }
}
