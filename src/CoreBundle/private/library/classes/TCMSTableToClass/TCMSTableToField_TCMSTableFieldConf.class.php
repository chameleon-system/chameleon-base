<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Doctrine\DBAL\Connection;

class TCMSTableToField_TCMSTableFieldConf extends TCMSTableToClass_MockRecord
{
    private $oFieldType;
    /**
     * @var TPkgCmsStringUtilities_ReadConfig
     */
    private $oCacheExtraConfigFieldObject;

    public function __construct(Connection $connection, TCMSTableToField_TCMSFieldType $oFieldType)
    {
        $this->oFieldType = $oFieldType;
        parent::__construct($connection, 'cms_field_conf');
    }

    public function GetFieldType()
    {
        return $this->oFieldType;
    }

    /**
     * get field type specific config parameter value for given key.
     *
     * @param string $parameterKey
     *
     * @return string
     */
    public function GetFieldtypeConfigKey($parameterKey)
    {
        if (null === $this->oCacheExtraConfigFieldObject) {
            $this->oCacheExtraConfigFieldObject = new TPkgCmsStringUtilities_ReadConfig($this->sqlData['fieldtype_config']);
        }

        return $this->oCacheExtraConfigFieldObject->getConfigValue($parameterKey);
    }
}
