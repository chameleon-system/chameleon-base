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
 * ;.
 * /**/
class TPkgCommentType extends TPkgCommentTypeAutoParent
{
    /**
     * Get instance for type.
     *
     * @param string|int $sPkgCommentTypeId
     *
     * @return TdbPkgCommentType|false
     */
    public function GetInstance($sPkgCommentTypeId)
    {
        $oPkgCommentType = TdbPkgCommentType::GetNewInstance();
        $oInstance = false;
        if ($oPkgCommentType->Load($sPkgCommentTypeId)) {
            $sClassName = $oPkgCommentType->fieldClassName;
            $oInstance = new $sClassName();
            $oInstance->LoadFromRow($oPkgCommentType->sqlData);
        } else {
            trigger_error('Unable to find comment type ['.$sPkgCommentTypeId.']', E_USER_WARNING);
        }

        return $oInstance;
    }

    /**
     * @return mixed|null
     */
    public function GetActiveItem()
    {
        return null;
    }

    /**
     * @param string $sCommentTypeId
     *
     * @return string
     *
     * @throws TPkgCmsException_Log
     */
    public static function GetCommentTypeTableName($sCommentTypeId)
    {
        $sTableName = '';
        $sQuery = "SELECT `cms_tbl_conf`.`name` FROM `pkg_comment_type`
                      INNER JOIN `cms_tbl_conf` ON `cms_tbl_conf`.`id` = `pkg_comment_type`.`cms_tbl_conf_id`
                      WHERE `pkg_comment_type`.`id` = '".MySqlLegacySupport::getInstance()->real_escape_string($sCommentTypeId)."'";
        $oRes = MySqlLegacySupport::getInstance()->query($sQuery);
        if (MySqlLegacySupport::getInstance()->num_rows($oRes) > 0) {
            $aRow = MySqlLegacySupport::getInstance()->fetch_assoc($oRes);
            $sTableName = $aRow['name'];
        }

        return $sTableName;
    }
}
