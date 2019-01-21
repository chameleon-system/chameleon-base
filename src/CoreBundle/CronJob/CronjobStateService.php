<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\CronJob;

use ChameleonSystem\CoreBundle\Exception\CronjobHandlingException;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;

class CronjobStateService implements CronjobStateServiceInterface
{
    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * {@inheritdoc}
     */
    public function isCronjobRunning(): bool
    {
        try {
            return $this->connection->fetchColumn("SELECT COUNT(*) FROM `cms_cronjobs` WHERE `lock` = '1'") > 0;
        } catch (DBALException $exception) {
            throw new CronjobHandlingException('Cannot check for cron jobs lock in database.', 0, $exception);
        }
    }
}
