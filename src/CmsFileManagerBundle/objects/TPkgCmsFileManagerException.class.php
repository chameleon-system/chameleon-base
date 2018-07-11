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
 * @deprecated since 6.2.0 - File manager no longer throws exceptions.
 */
class TPkgCmsFileManagerException extends TPkgCmsException_Log
{
    public function getLogFilePath()
    {
        $this->logFilePath = 'fileManagerSyncErrors.log';

        return $this->logFilePath;
    }
}
