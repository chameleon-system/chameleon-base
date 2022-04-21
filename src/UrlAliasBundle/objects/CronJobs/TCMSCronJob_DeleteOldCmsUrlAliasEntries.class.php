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
 * cleanup old url alias entries.
/**/
class TCMSCronJob_DeleteOldCmsUrlAliasEntries extends TdbCmsCronjobs
{
    /**
     * @return void
     */
    protected function _ExecuteCron()
    {
        $iCutoff = time();
        $sCutoff = date('Y-m-d H:i:s', $iCutoff);
        $query = "UPDATE  `cms_url_alias` SET  `active` = '0' WHERE  `active` = '1' AND `expiration_date` > '0000-00-00 00:00:00'  AND `expiration_date` <= '".MySqlLegacySupport::getInstance()->real_escape_string($sCutoff)."'";
        MySqlLegacySupport::getInstance()->query($query);
        $iAffected = MySqlLegacySupport::getInstance()->affected_rows();
        if ($iAffected > 0) {
            TCacheManager::PerformeTableChange('cms_url_alias');
        }
    }
}
