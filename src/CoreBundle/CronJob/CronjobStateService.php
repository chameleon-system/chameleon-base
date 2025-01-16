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

use ChameleonSystem\CoreBundle\CronJob\DataModel\CronJobDataModel;
use ChameleonSystem\CoreBundle\Exception\CronjobHandlingException;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

class CronjobStateService implements CronjobStateServiceInterface
{
    public function __construct(
        private readonly Connection $connection,
        private readonly \TTools $tools)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function isCronjobRunning(): bool
    {
        try {
            return $this->connection->fetchOne("SELECT COUNT(*) FROM `cms_cronjobs` WHERE `lock` = '1'") > 0;
        } catch (Exception $exception) {
            throw new CronjobHandlingException('Cannot check for cron jobs lock in database.', 0, $exception);
        }
    }

    /**
     * @return array<CronJobDataModel>
     */
    public function getLastRunCronJobs(int $limit = 5): array
    {
        $query = 'SELECT * FROM `cms_cronjobs` ORDER BY `real_last_execution` DESC LIMIT '.$limit;

        $cronJobList = \TdbCmsCronjobsList::GetList($query);

        $cronJobDataModels = [];
        while ($cronJob = $cronJobList->Next()) {
            $cronJobDataModels[] = $this->createCronJobDataModel($cronJob);
        }

        return $cronJobDataModels;
    }

    /**
     * @return array<CronJobDataModel>
     */
    public function getRunningRunCronJobs(int $limit = 5): array
    {
        $query = "SELECT *
                    FROM `cms_cronjobs`
                   WHERE `lock` = '1'
                ORDER BY `real_last_execution` DESC
                   LIMIT ".$limit;

        $cronJobList = \TdbCmsCronjobsList::GetList($query);

        $cronJobDataModels = [];
        while ($cronJob = $cronJobList->Next()) {
            $cronJobDataModels[] = $this->createCronJobDataModel($cronJob);
        }

        return $cronJobDataModels;
    }

    private function createCronJobDataModel(\TdbCmsCronjobs $cronJob): CronJobDataModel
    {
        $cronJobScheduleDataModel = $cronJob->getSchedule();

        return new CronJobDataModel(
            $cronJob->id,
            $cronJob->GetName(),
            $cronJob->fieldActive,
            $cronJobScheduleDataModel,
            $this->getRecordEditUrl($cronJob)
        );
    }

    private function getRecordEditUrl(\TdbCmsCronjobs $cronJob): string
    {
        $tableId = $this->getCronJobTableId();

        return '/cms?pagedef=tableeditor&tableid='.$tableId.'&id='.$cronJob->id;
    }

    private function getCronJobTableId(): string
    {
        return $this->tools::GetCMSTableId('cms_cronjobs');
    }
}
