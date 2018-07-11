<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TCMSCronJob_CleanTags extends TCMSCronJob
{
    protected function _ExecuteCron()
    {
        $query = "DELETE
                  FROM `cms_tags`
                 WHERE `count` < '1'";

        MySqlLegacySupport::getInstance()->query($query);
    }
}
