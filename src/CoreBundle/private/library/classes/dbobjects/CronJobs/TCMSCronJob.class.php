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
use ChameleonSystem\CoreBundle\Interfaces\TimeProviderInterface;
use ChameleonSystem\CoreBundle\ServiceLocator;

/**
 * manages a cronjob.
 */
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
            $sMessage = sprintf(
                'Cronjob "%s" failed with PHP error: %s [pid: %s]',
                $this->sqlData['name'],
                $error->getMessage(),
                getmypid()
            );
            $this->getLogger()->critical(
                $sMessage,
                __FILE__,
                __LINE__,
                array(
                    'fullMessage' => $error->getMessage(),
                    'trace'       => $error->getTraceAsString(),
                )
            );
        }
        $this->AddMessageOutput($sMessage);
    }

    private function getCronJobScheduler(): CronJobSchedulerInterface
    {
        return ServiceLocator::get('chameleon_system_core.cron_job.scheduler');
    }

    /**
     * @return CronJobScheduleDataModel
     *
     * @throws InvalidArgumentException
     */
    private function getSchedule(): CronJobScheduleDataModel
    {
        $lastPlannedExecution = null;
        if ('' !== $this->sqlData['last_execution']) {
            $lastPlannedExecution = \DateTime::createFromFormat(
                'Y-m-d H:i:s',
                $this->sqlData['last_execution']
            );
        }

        $executeEveryNMinutes = (int)$this->sqlData['execute_every_n_minutes'];

        if (0 === $executeEveryNMinutes) {
            throw new InvalidArgumentException(
                sprintf(
                    'CronJob %s has an invalid value of "%s" for the execute_every_n_minutes field',
                    $this->sqlData['name'],
                    $this->sqlData['execute_every_n_minutes']
                )
            );
        }

        return new CronJobScheduleDataModel(
            $executeEveryNMinutes,
            (int)$this->sqlData['unlock_after_n_minutes'],
            '1' === $this->sqlData['lock'],
            $lastPlannedExecution
        );
    }

    /**
     * updates the last execution time.
     */
    protected function _UpdateLastExecutionTime()
    {
        $scheduler = $this->getCronJobScheduler();
        try {
            $schedule = $this->getSchedule();
        } catch (InvalidArgumentException $e) {
            $this->getLogger()->error($e->getMessage(), __FILE__, __LINE__);

            return;
        }
        $timeProvider = $this->getTimeProvider();
        $now = $timeProvider->getDateTime();

        $plannedExecutionTime = $scheduler->calculateCurrentPlannedExecutionDate($schedule);

        $this->getDatabaseConnection()->update(
            $this->table,
            [
                'last_execution'      => $plannedExecutionTime->format('Y-m-d H:i:s'),
                'real_last_execution' => $now->format('Y-m-d H:i:s'),
            ],
            ['id' => $this->id]
        );
    }

    protected function UpdateLastExecutionOnStart()
    {
        $now = new \DateTime('now');

        $this->getDatabaseConnection()->update(
            $this->table,
            [
                'last_execution' => $now->format('Y-m-d H:i:s'),
            ],
            ['id' => $this->id]
        );
    }

    /**
     * checks if this cronJob needs to be executed.
     *
     * @return bool
     */
    protected function _NeedExecution()
    {
        $scheduler = $this->getCronJobScheduler();
        try {
            $schedule = $this->getSchedule();
        } catch (InvalidArgumentException $e) {
            $this->getLogger()->error($e->getMessage(), __FILE__, __LINE__);

            return false;
        }

        $requiresExecution = $scheduler->requiresExecution($schedule);
        if (true === $requiresExecution && true === $this->isLocked()) {
            $this->getLogger()->warning(
                sprintf(
                    'Cron job "%s" (%s) was force unlocked due to it being locked for longer than its unlock_after_n_minutes value',
                    $this->sqlData['name'],
                    $this->id
                ),
                __FILE__,
                __LINE__,
                ['schedule' => $schedule]
            );
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
        $numberOfRowsAffected = $this->getDatabaseConnection()->update(
            $this->table,
            ['lock' => '1'],
            ['id' => $this->id]
        );
        $this->sqlData['lock'] = '1';

        return 1 === $numberOfRowsAffected;
    }

    /**
     * unlocks the cronjob.
     */
    public function _Unlock()
    {
        $this->getDatabaseConnection()->update(
            $this->table,
            ['lock' => '0'],
            ['id' => $this->id]
        );
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

    private function getTimeProvider(): TimeProviderInterface
    {
        return ServiceLocator::get('chameleon_system_core.system_time_provider');
    }

    protected function isLocked(): bool
    {
        return '1' === $this->sqlData['lock'];
    }
}
