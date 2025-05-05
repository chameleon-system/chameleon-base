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
 * deletes old tokens.
 * /**/
class TCMSCronJob_ClearOldDownloadTokens extends TdbCmsCronjobs
{
    protected function _ExecuteCron()
    {
        $this->clearAuthenticityToken();
    }

    /**
     * clears document security hash table, removes expired and invalid Authenticity Token.
     */
    protected function clearAuthenticityToken()
    {
        $sQuery = "DELETE FROM `cms_document_security_hash`
        WHERE `cms_document_security_hash`.`enddate` < '".MySqlLegacySupport::getInstance()->real_escape_string(date('Y-m-d H:i:s'))."'
        OR `cms_document_security_hash`.`cms_document_id` = ''";
        MySqlLegacySupport::getInstance()->query($sQuery);
    }
}
