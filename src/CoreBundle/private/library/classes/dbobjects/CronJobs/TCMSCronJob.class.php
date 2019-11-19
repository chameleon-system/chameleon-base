<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\CronJob\CronJobScheduleDataModel;
use ChameleonSystem\CoreBundle\CronJob\CronJobSchedulerInterface;
use ChameleonSystem\CoreBundle\ServiceLocator;

/**
 * manages a cronjob.
/**/
class TCMSCronJob extends TCMSRecord
{
    /**
     * @deprecated since 6.0.9 - use a logging service instead
     */
    const LOG_FILE = '/logs/cronjobs.log';

    /**
     * add optional message output to this property
     * the message is rendered only in backend mode.
     */
    protected $sMessageOutput = '';

    public function TCMSCronJob($id = null)
    {
        parent::TCMSRecord('cms_cronjobs', $id);
    }

    /**
     * getter for $this->sMessageOutput.
     *
     * @return string
     */
    public function GetMessageOutput()
    {
        return $this->sMessageOutput;
    }

    /**
     * setter for $this->sMessageOutput
     * adds additional lines to $this->sMessageOutput.
     *
     * @param string $sMessage
     */
    public function AddMessageOutput($sMessage)
    {
        $this->sMessageOutput .= $sMessage."\n";
    }

    /**
     * @return IPkgCmsCoreLog
     */
    protected function getLogger()
    {
        return ServiceLocator::get('cmsPkgCore.logChannel.cronjobs');
    }

    /**
     * checks if cronjob needs to be executed and executes it if necessary
     * logs cronjob execution.
     *
     * @param bool $bForceExecution
     */
    public function RunScript($bForceExecution = false)
    {
        if (false === $bForceExecution && false === $this->_NeedExecution()) {
            return;
        }

        if (false === $this->_Lock()) {
            return;
        }

        $this->getLogger()->info(
            sprintf('Cronjob "%s" started. [pid: %s]', $this->sqlData['name'], getmypid()),
            __FILE__,
            __LINE__,
            array('job' => $this->sqlData)
        );

        if (false === $bForceExecution) {
            $this->UpdateLastExecutionOnStart();
        }
        $originalErrorHandler = $this->setExceptionErrorHandler();
        $error = null;
        try {
            $this->_ExecuteCron();
        } catch (Error $e) {
            $error = $e;
        } catch (Exception $e) {
            $error = $e;
        }

        $this->setErrorHandler($originalErrorHandler);

        if (false === $bForceExecution) {
            $this->_UpdateLastExecutionTime();
        }
        $this->_Unlock();

        $this->outputResult($error);
    }

    /**
     * executes the cron job (add your custom method calls here).
     */
    protected function _ExecuteCron()
    {
    }

    /**
     * @param Exception|Error $error
     */
    private function outputResult($error)
    {
        if (null === $error) {
            $sMessage = sprintf('Cronjob "%s" completed. [pid: %s]', $this->sqlData['name'], getmypid());
            $this->getLogger()->info($sMessage, __FILE__, __LINE__);
        } else {
            $sMessage = sprintf('Cronjob "%s" failed with PHP error: %s [pid: %s]', $this->sqlData['name'], $error->getMessage(), getmypid());
            $this->getLogger()->critical($sMessage, __FILE__, __LINE__, array(
                'fullMessage' => $error->getMessage(),
                'trace' => $error->getTraceAsString(),
            ));
        }
        $this->AddMessageOutput($sMessage);
    }

    private function getCronJobScheduler(): CronJobSchedulerInterface
    {
        return ServiceLocator::get('chameleon_system_core.cron_job.cron_job_scheduler');
    }
    /**
     * updates the last execution time.
     */
    protected function _UpdateLastExecutionTime()
    {
        // maybe the cron was deactivated... the new time should be less than n minutes before now
        // set server timezone to daylight saving time less region (UTC) to prevent cron job time shifts by 1 hour during daylight saving times
        $sServerDateTimeZoneSetting = date_default_timezone_get();
        date_default_timezone_set('UTC');

        $utc = new \DateTimeZone('UTC');
        $nowObject = new \DateTime('now', $utc);
        $executionBeforeNow = clone $nowObject;
        $executionBeforeNow->sub(new DateInterval(sprintf('P%sM', $this->sqlData['execute_every_n_minutes'])));
        $now = time();
        if ('' === $this->sqlData['last_execution']) {
            $newTimeObject = DateTime::createFromFormat('Y-m-d H:i:s', $this->sqlData['start_execution']. ' 00:00:00', $utc);
        } else {
            $newTimeObject = DateTime::createFromFormat('Y-m-d H:i:s', $this->sqlData['last_execution'], $utc);
        }

//        $newTime = strtotime($this->sqlData['last_execution']);
//        if ($newTime <= 0) {
//            $newTime = 1;
//        } // fixes empty last_execution field (would lead to infinite loop)
//        if (empty($this->sqlData['last_execution'])) {
//            $newTime = $now;
//        } else {
            do {
                $newTimeObject = $newTimeObject->add(new DateInterval(sprintf('P%sM', $this->sqlData['execute_every_n_minutes'])));
                //$newTime = $newTime + (60 * $this->sqlData['execute_every_n_minutes']);
            //} while ($newTime < $now - (60 * $this->sqlData['execute_every_n_minutes']));
            } while ($newTimeObject < $executionBeforeNow);
//        }

        $query = 'UPDATE `'.MySqlLegacySupport::getInstance()->real_escape_string($this->table)."`
                   SET `last_execution` = '".MySqlLegacySupport::getInstance()->real_escape_string($newTimeObject->format('Y-m-d H:i:s'))."',
                   `real_last_execution` = '".MySqlLegacySupport::getInstance()->real_escape_string($nowObject->format('Y-m-d H:i:s'))."'
                 WHERE `id` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->id)."'";
        MySqlLegacySupport::getInstance()->query($query);

        date_default_timezone_set($sServerDateTimeZoneSetting);
    }

    protected function UpdateLastExecutionOnStart()
    {
        $utc = new \DateTimeZone('UTC');
        $nowObject = new \DateTime('now', $utc);

        $query = 'UPDATE `'.MySqlLegacySupport::getInstance()->real_escape_string($this->table)."`
                   SET `last_execution` = '".MySqlLegacySupport::getInstance()->real_escape_string($nowObject->format('Y-m-d H:i:s'))."'
                 WHERE `id` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->id)."'";
        MySqlLegacySupport::getInstance()->query($query);
    }

    /**
     * checks if this cronJob needs to be executed.
     *
     * @return bool
     */
    protected function _NeedExecution()
    {
        $scheduler = $this->getCronJobScheduler();
        $utc = new \DateTimeZone('UTC');
        $lastPlannedExecution = null;
        if ('' !== $this->sqlData['last_execution']) {
            $lastPlannedExecution = \DateTime::createFromFormat('Y-m-d H:i:s', $this->sqlData['last_execution'], $utc);
        }
        $schedule = new CronJobScheduleDataModel(
            $this->sqlData['execute_every_n_minutes'],
            $this->sqlData['unlock_after_n_minutes'],
            '1' === $this->sqlData['lock'],
            $lastPlannedExecution
        );

        $requiresExecution = $scheduler->requiresExecution($schedule);
        if ($requiresExecution && '1' === $this->sqlData['lock']) {
            $this->_Unlock();
        }

        return $requiresExecution;
    }

    /**
     * locks the cronjob as to prevent double execution.
     *
     * @return bool
     */
    public function _Lock()
    {
        $query = 'UPDATE `'.MySqlLegacySupport::getInstance()->real_escape_string($this->table)."`
                   SET `lock` = '1'
                 WHERE `id` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->id)."'";
        MySqlLegacySupport::getInstance()->query($query);
        $this->sqlData['lock'] = '1';

        return 1 == MySqlLegacySupport::getInstance()->affected_rows();
    }

    /**
     * unlocks the cronjob.
     */
    public function _Unlock()
    {
        $query = 'UPDATE `'.MySqlLegacySupport::getInstance()->real_escape_string($this->table)."`
                   SET `lock` = '0'
                 WHERE `id` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->id)."'";
        MySqlLegacySupport::getInstance()->query($query);
        $this->sqlData['lock'] = '0';
    }

    /**
     * @return callable
     **/
    private function setExceptionErrorHandler()
    {
        $failureErrorLevel = $this->getFailureErrorLevel();

        return set_error_handler(
            function ($severity, $message, $file, $line) use ($failureErrorLevel) {
                if (0 === ($failureErrorLevel & $severity)) {
                    return;
                }
                throw new ErrorException($message, 0, $severity, $file, $line);
            }
        );
    }

    private function getFailureErrorLevel(): int
    {
        return ServiceLocator::getParameter('chameleon_system_core.cronjobs.fail_on_error_level');
    }

    /**
     * @param callable $errorHandler
     */
    private function setErrorHandler($errorHandler)
    {
        set_error_handler($errorHandler);
    }
}
