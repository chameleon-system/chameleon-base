<?php

declare(strict_types=1);

namespace ChameleonSystem\CoreBundle\Controller;

use ChameleonSystem\CoreBundle\Service\CronJobServiceInterface;
use Symfony\Component\HttpFoundation\Response;

class CronJobController
{
    public function __construct(
        private readonly CronJobServiceInterface $cronJobService,
    ) {
    }

    public function run(): Response
    {
        return $this->cronJobService->runCronjobs();
    }
}
