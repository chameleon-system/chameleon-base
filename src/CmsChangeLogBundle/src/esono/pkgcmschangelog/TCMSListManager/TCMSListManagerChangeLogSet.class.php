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
        $jsParas = ['id'];

        ++$this->columnCount;
        $this->tableObj->AddHeaderField(['cms_tbl_conf' => ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_cms_change_log.column.changed_table')], 'left', null, 1, false);
        $this->tableObj->AddColumn('cms_tbl_conf', 'left', [$this, 'CallbackResolveTableName'], $jsParas, 1);

        ++$this->columnCount;
        $this->tableObj->AddHeaderField(['modified_name' => ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_cms_change_log.column.changed_record')], 'left', null, 1, false);
        $this->tableObj->AddColumn('modified_name', 'left', null, $jsParas, 1);
        $this->tableObj->searchFields['`pkg_cms_changelog_set`.`modified_name`'] = 'full'; // allow searching in this field

        ++$this->columnCount;
        $this->tableObj->AddHeaderField(['modify_date' => ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_cms_change_log.column.changed_on')], 'left', null, 1, false);
        $this->tableObj->AddColumn('modify_date', 'left', [$this, 'CallbackFormatDate'], $jsParas, 1);

        ++$this->columnCount;
        $this->tableObj->AddHeaderField(['cms_user' => ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_cms_change_log.column.changed_by')], 'left', null, 1, false);
        $this->tableObj->AddColumn('cms_user', 'left', [$this, 'CallbackFormatUser'], $jsParas, 1);
        $this->tableObj->searchFields['`pkg_cms_changelog_set`.`cms_user`'] = 'full'; // allow searching in this field

        ++$this->columnCount;
        $this->tableObj->AddHeaderField(['change_type' => ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_cms_change_log.column.change_type')], 'left', null, 1, false);
        $this->tableObj->AddColumn('change_type', 'left', [$this, 'CallbackFormatChangeType'], $jsParas, 1);
    }

    /**
     * @param string $cellValue
     * @param array<string, mixed> $row
     * @param string $name
     *
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
     *
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
     *
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
     *
     * @return string|null
     */
    public function CallbackFormatChangeType($cellValue, $row, $name)
    {
        return TCMSChangeLogFormatter::formatChangeType($cellValue);
    }
}
