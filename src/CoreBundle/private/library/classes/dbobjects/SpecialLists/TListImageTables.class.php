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
 * class loads all table confs that contain fields which may hold the image.
 *
 * @extends TCMSRecordList<TCMSTableConf>
 */
class TListImageTables extends TCMSRecordList
{
    public function __construct()
    {
        parent::__construct($sTableObject = 'TCMSTableConf', 'cms_tbl_conf', $this->_GetQuery());
    }

    /**
     * @deprecated Named constructors are deprecated and will be removed with PHP8. When calling from a parent, please use `parent::__construct` instead.
     * @see self::__construct
     */
    public function TListImageTables()
    {
        $this->callConstructorAndLogDeprecation(func_get_args());
    }

    protected function _GetQuery()
    {
        $imageFieldTypes = TCMSFieldDefinition::GetImageFieldTypes();

        $databaseConnection = $this->getDatabaseConnection();
        $imageFieldTypeString = implode(',', array_map(array($databaseConnection, 'quote'), $imageFieldTypes));

        $query = "
        SELECT DISTINCT `cms_tbl_conf`.*
          FROM `cms_tbl_conf`
    INNER JOIN `cms_field_conf` ON `cms_tbl_conf`.`id` = `cms_field_conf`.`cms_tbl_conf_id`
    INNER JOIN `cms_field_type` ON `cms_field_conf`.`cms_field_type_id` = `cms_field_type`.`id`
         WHERE `cms_field_type`.`constname` IN ($imageFieldTypeString)
      ";

        return $query;
    }
}
