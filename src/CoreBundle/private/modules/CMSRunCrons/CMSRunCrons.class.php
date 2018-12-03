<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\CronJob\CronjobEnablingServiceInterface;
use ChameleonSystem\CoreBundle\CronJob\CronJobFactoryInterface;
use ChameleonSystem\CoreBundle\SanityCheck\CronJobDataAccess;
use ChameleonSystem\CoreBundle\ServiceLocator;

/**
 * runs one explicit or all cronjobs.
 *
/**/
class CMSRunCrons extends TModelBase
{
    public function &Execute()
    {
        $dataAccess = new CronJobDataAccess();
        $dataAccess->refreshTimestampOfLastCronJobCall();
        set_time_limit(CMS_MAX_EXECUTION_TIME_IN_SECONDS_FOR_CRONJOBS);
        $this->data = parent::Execute();
        $this->data['sMessageOutput'] = '';

        if ($this->global->UserDataExists('cronjobid') && TCMSUser::CMSUserDefined()) {
            /*
            * run explicit cronjob (needs valid CMS user session), called from backend
            * cronjob execution is forced (ignores active and last run, but checks for lock)
            */
            $sCronID = $this->global->GetUserData('cronjobid');
            if (!empty($sCronID)) {
                $oTdbCmsCronJob = TdbCmsCronjobs::GetNewInstance();
                $oTdbCmsCronJob->Load($sCronID);
                if (false === $oTdbCmsCronJob->fieldLock && true === $this->isCronjobsEnabled()) {
                    $this->RunCronJob($oTdbCmsCronJob, true);
                }
            }
        } else { // run all active cronjobs
            // unlock all cron jobs that are past their max lock time
            $this->unlockOldBlockedCronJobs();

            $now = date('Y-m-d');
            $sQuery = "SELECT * FROM `cms_cronjobs`
                   WHERE `start_execution` <= '".MySqlLegacySupport::getInstance()->real_escape_string($now)."'
                     AND (`end_execution` >= '".MySqlLegacySupport::getInstance()->real_escape_string($now)."' || `end_execution` = '0000-00-00')
                     AND `active` = '1'
                ORDER BY `execute_every_n_minutes`
                 ";
            $oTdbCmsCronjobsList = TdbCmsCronjobsList::GetList($sQuery);
            /** @var $oTdbCmsCronJob TdbCmsCronjobs */
            while ($oTdbCmsCronJob = $oTdbCmsCronjobsList->Next()) {
                if (false === $this->isCronjobsEnabled()) {
                    break;
                }

                $this->RunCronJob($oTdbCmsCronJob);
            }
        }

        return $this->data;
    }

    private function isCronjobsEnabled(): bool
    {
        return $this->getCronjobActivationService()->isCronjobExecutionEnabled();
    }

    protected function unlockOldBlockedCronJobs()
    {
        $iTime = time();

        $query = "SELECT *
                    FROM `cms_cronjobs`
                   WHERE `lock` = '1'
                     AND `active` = '1'
                     AND `unlock_after_n_minutes` > 0
                     AND ((`unlock_after_n_minutes`*60) + UNIX_TIMESTAMP(`last_execution`) <= {$iTime})
        ";
        $oCronJobList = TdbCmsCronjobsList::GetList($query);
        if ($oCronJob = $oCronJobList->Next()) {
            $oCronJobObject = $this->cronJobClassFactory($oCronJob);
            $oCronJobObject->_Unlock();
        }
    }

    /**
     * load a cron job class (TCMSCronJob) and executes it (calls "RunScript").
     *
     * @param TdbCmsCronjobs $oTdbCmsCronJob
     * @param bool           $bForceExecution
     */
    protected function RunCronJob($oTdbCmsCronJob, $bForceExecution = false)
    {
        $oCronjob = $this->cronJobClassFactory($oTdbCmsCronJob);
        $oCronjob->RunScript($bForceExecution);
        if (TCMSUser::CMSUserDefined()) {
            $this->data['sMessageOutput'] = $oCronjob->GetMessageOutput();
        }
    }

    /**
     * @param TCMSCronJob $oTdbCmsCronJob
     *
     * @return TCMSCronJob
     */
    private function cronJobClassFactory($oTdbCmsCronJob)
    {
        return $this->getCronjobFactory()->constructCronJob($oTdbCmsCronJob->sqlData['cron_class'], $oTdbCmsCronJob->sqlData);
    }

    /**
     * @return CronJobFactoryInterface
     */
    private function getCronjobFactory()
    {
        return ServiceLocator::get('chameleon_system_core.cronjob.cronjob_factory');
    }

    private function getCronjobActivationService(): CronjobEnablingServiceInterface
    {
        return ServiceLocator::get('chameleon_system_core.cronjob.cronjob_enabling_service');
    }
}
