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
 * Cronjob for archiving changelog data.
/**/
class TCMSCronJob_ArchiveChangeLog extends TCMSCronJob
{
    protected function _ExecuteCron()
    {
        $archiver = new TCMSChangeLogArchiver();
        $archiver->archiveAndDelete();
    }
}
