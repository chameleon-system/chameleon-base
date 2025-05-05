<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\AutoclassesBundle\DataAccess;

use TCMSConfig;

interface AutoclassesDataAccessInterface
{
    /**
     * Returns contents of the cms_tbl_extension table.
     *
     * @return array
     */
    public function getTableExtensionData();

    /**
     * Returns contents of the cms_field_conf table as well as the table name the respective field.
     *
     * @return array
     */
    public function getFieldData();

    /**
     * Returns a TCMSConfig instance (not a TdbCmsConfig instance because we cannot expect that this class is already
     * generated when this method is called).
     *
     * @return \TCMSConfig
     */
    public function getConfig();

    /**
     * Returns contents of the cms_tbl_display_orderfields table (some fields only).
     *
     * @return array
     */
    public function getTableOrderByData();

    /**
     * Returns contents of the cms_tbl_conf table.
     *
     * @return array
     */
    public function getTableConfigData();
}
