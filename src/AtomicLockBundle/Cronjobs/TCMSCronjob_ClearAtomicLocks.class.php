<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TCMSCronjob_ClearAtomicLocks extends TdbCmsCronjobs
{
    public function _ExecuteCron()
    {
        $this->clearTable();
    }

    private function clearTable()
    {
        $sQuery = 'TRUNCATE TABLE `data_atomic_lock`';
        MySqlLegacySupport::getInstance()->query($sQuery);
    }
}
