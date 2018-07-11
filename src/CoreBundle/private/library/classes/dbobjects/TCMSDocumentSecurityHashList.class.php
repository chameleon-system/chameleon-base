<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TCMSDocumentSecurityHashList extends TCMSDocumentSecurityHashListAutoParent
{
    /**
     * returns list filtered by document id, valid time span and extranet user id.
     *
     * @param string $sDocumentID
     * @param string $sUserID
     *
     * @return TdbCmsDocumentSecurityHashList
     */
    public static function getListForDocumentAndUser($sDocumentID, $sUserID)
    {
        $sQuery = "SELECT * FROM `cms_document_security_hash`
        WHERE
        `cms_document_security_hash`.`cms_document_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($sDocumentID)."'
        AND `cms_document_security_hash`.`data_extranet_user_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($sUserID)."'
        AND UNIX_TIMESTAMP() > UNIX_TIMESTAMP(`cms_document_security_hash`.`publishdate`)
        AND UNIX_TIMESTAMP() < UNIX_TIMESTAMP(`cms_document_security_hash`.`enddate`)";

        $oRecordList = TdbCmsDocumentSecurityHashList::GetList($sQuery);

        return $oRecordList;
    }
}
