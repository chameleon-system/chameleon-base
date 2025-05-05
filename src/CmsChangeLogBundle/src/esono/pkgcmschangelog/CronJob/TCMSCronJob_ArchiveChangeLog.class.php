<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CmsChangeLog\Exception\CmsChangeLogDataAccessFailedException;
use ChameleonSystem\CmsChangeLog\Interfaces\CmsChangeLogDataAccessInterface;
use Psr\Log\LoggerInterface;

// TODO rename this and it's id as it only deletes now?
class TCMSCronJob_ArchiveChangeLog extends TdbCmsCronjobs
{
    private int $days;
    private CmsChangeLogDataAccessInterface $changeLogDataAccess;
    private LoggerInterface $logger;

    public function __construct(CmsChangeLogDataAccessInterface $changeLogDataAccess, LoggerInterface $logger, int $days)
    {
        parent::__construct(null, null);
        $this->days = $days;
        $this->changeLogDataAccess = $changeLogDataAccess;
        $this->logger = $logger;
    }

    /**
     * @return void
     */
    protected function _ExecuteCron()
    {
        if ($this->days <= 0) {
            return;
        }

        try {
            $changedEntries = $this->changeLogDataAccess->deleteOlderThan($this->days);

            if ($changedEntries > 0) {
                $this->logger->info(sprintf('Deleted %s change log entries older than %s days.', $changedEntries, $this->days));
            }
        } catch (CmsChangeLogDataAccessFailedException $exception) {
            $this->logger->error($exception->getMessage(), ['exception' => $exception]);
        }
    }
}
