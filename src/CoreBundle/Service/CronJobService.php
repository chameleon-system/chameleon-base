<?php

namespace ChameleonSystem\CoreBundle\Service;

use ChameleonSystem\CoreBundle\CronJob\CronjobEnablingServiceInterface;
use ChameleonSystem\CoreBundle\CronJob\CronJobFactoryInterface;
use ChameleonSystem\CoreBundle\DataAccess\CronJobDataAccess;
use ChameleonSystem\SecurityBundle\Service\SecurityHelperAccess;
use ChameleonSystem\SecurityBundle\Voter\CmsUserRoleConstants;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class CronJobService implements CronJobServiceInterface
{
    private array $messagesOutput = [];

    public function __construct(
        private readonly CronJobFactoryInterface $cronjobFactory,
        private readonly CronjobEnablingServiceInterface $cronjobEnablingService,
        private readonly LoggerInterface $logger,
        private readonly TranslatorInterface $translator,
        private readonly Connection $databaseConnection,
        private readonly CronJobDataAccess $cronjobDataAccess,
        private readonly SecurityHelperAccess $securityHelperAccess,
        private readonly RequestStack $requestStack,
        private readonly \ViewRenderer $viewRenderer
    ) {
    }

    public function runCronjobs(): Response
    {
        $this->cronjobDataAccess->refreshTimestampOfLastCronJobCall();
        set_time_limit(CMS_MAX_EXECUTION_TIME_IN_SECONDS_FOR_CRONJOBS);

        $currentRequest = $this->requestStack->getCurrentRequest();

        if (null === $currentRequest) {
            return $this->createBadRequestResponse('chameleon_system_core.cronjob.error_request_not_available');
        }

        $cronjobId = $currentRequest->get('cronjobid', null);
        if ($this->shouldRunSpecificCronjob($cronjobId)) {
            return $this->handleSpecificCronjob($cronjobId);
        }

        return $this->handleAllCronjobs();
    }

    protected function unlockOldBlockedCronJobs(): void
    {
        $timeStamp = time();

        $query = "SELECT *
                    FROM `cms_cronjobs`
                   WHERE `lock` = '1'
                     AND `active` = '1'
                     AND `unlock_after_n_minutes` > 0
                     AND ((`unlock_after_n_minutes`*60) + UNIX_TIMESTAMP(`last_execution`) <= ".$this->databaseConnection->quote($timeStamp).')
        ';
        $cronjobList = \TdbCmsCronjobsList::GetList($query);
        if ($cronjob = $cronjobList->Next()) {
            $cronJobObject = $this->cronJobClassFactory($cronjob);
            $cronJobObject->_Unlock();
        }
    }

    protected function runCronJob(\TdbCmsCronjobs $cmsCronjobs, bool $forceExecution = false): void
    {
        $cronjob = $this->cronJobClassFactory($cmsCronjobs);
        $cronjob->RunScript($forceExecution);

        if ($this->securityHelperAccess->isGranted(CmsUserRoleConstants::CMS_USER)) {
            $this->messagesOutput[] = $cronjob->GetMessageOutput();
        }
    }

    private function shouldRunSpecificCronjob(?string $cronjobId): bool
    {
        return null !== $cronjobId
            && '' !== $cronjobId
            && $this->securityHelperAccess->isGranted(CmsUserRoleConstants::CMS_USER);
    }

    private function cronJobClassFactory(\TCMSCronJob $cmsCronJob): \TCMSCronJob
    {
        return $this->cronjobFactory->constructCronJob($cmsCronJob->sqlData['cron_class'], $cmsCronJob->sqlData);
    }

    private function handleSpecificCronjob(string $cronjobId): Response
    {
        if (!$this->cronjobEnablingService->isCronjobExecutionEnabled()) {
            return $this->createServiceUnavailableResponse('chameleon_system_core.cronjob.error_cronjobs_disabled');
        }

        $cronJob = \TdbCmsCronjobs::GetNewInstance();

        if (false === $cronJob->Load($cronjobId)) {
            return $this->createBadRequestResponse('chameleon_system_core.cronjob.cronjob_id_not_found');
        }

        if ($cronJob->fieldLock) {
            return $this->createServiceUnavailableResponse('chameleon_system_core.cronjob.error_cronjob_still_locked');
        }

        $this->runCronJob($cronJob, true);

        return $this->createSuccessResponse();
    }

    private function handleAllCronjobs(): Response
    {
        $this->unlockOldBlockedCronJobs();

        if (false === $this->cronjobEnablingService->isCronjobExecutionEnabled()) {
            $this->logger->info('Executing all cronjobs was disabled before starting.');

            return $this->createServiceUnavailableResponse('chameleon_system_core.cronjob.error_cronjob_disabled_before_starting');
        }

        try {
            $cronJobs = $this->loadDueActiveCronjobs();
        } catch (\Exception $exception) {
            $this->logger->error('Unable to load due active cronjobs');

            return new Response('Unable to load due active cronjobs', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        foreach ($cronJobs as $cronJobRow) {
            $cronJob = \TdbCmsCronjobs::GetNewInstance();

            // each job from db just before execution.
            if (false === $cronJob->Load($cronJobRow['id'])) {
                continue;
            }

            if (false === $cronJob->fieldActive) {
                continue;
            }

            if ('0000-00-00' !== $cronJob->fieldEndExecution && $cronJob->fieldEndExecution < date('Y-m-d')) {
                continue;
            }

            if (false === $this->cronjobEnablingService->isCronjobExecutionEnabled()) {
                $this->logger->info(sprintf('Cronjob execution was disabled before executing %s', $cronJob->id));

                return $this->createSuccessResponse();
            }

            $this->runCronJob($cronJob);
        }

        return $this->createSuccessResponse();
    }

    /**
     * @throws Exception
     */
    private function loadDueActiveCronjobs(): array
    {
        $today = date('Y-m-d');

        $query = <<<SQL
SELECT `id` FROM `cms_cronjobs`
WHERE `start_execution` <= :now
  AND (`end_execution` >= :now OR `end_execution` = '0000-00-00')
  AND `active` = '1'
ORDER BY `execute_every_n_minutes`
SQL;

        return $this->databaseConnection->fetchAllAssociative($query, ['now' => $today]);
    }

    private function createBadRequestResponse(string $translationKey): Response
    {
        return new Response($this->translator->trans($translationKey), Response::HTTP_BAD_REQUEST);
    }

    private function createServiceUnavailableResponse(string $translationKey): Response
    {
        return new Response($this->translator->trans($translationKey), Response::HTTP_SERVICE_UNAVAILABLE);
    }

    private function createSuccessResponse(): Response
    {
        $this->viewRenderer->AddSourceObject('cronjobMessages', $this->messagesOutput);
        try {
            $pageHtml = $this->viewRenderer->render('BackendLayout/cronjobs.html.twig');

            $response = new Response($pageHtml, Response::HTTP_OK);
            $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');
            $response->headers->set('Content-Type', 'text/html; charset=UTF-8');

            return $response;
        } catch (\Exception $e) {
            $this->logger->error('Error creating cronjob html response: {errorMessage}', ['errorMessage' => $e->getMessage()]);

            return $this->createServiceUnavailableResponse('chameleon_system_core.cronjob.error_rendering_view');
        }
    }
}
