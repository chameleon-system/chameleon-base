<?php

namespace ChameleonSystem\CoreBundle\Service;

use Symfony\Component\HttpFoundation\Response;

interface CronJobServiceInterface
{
    public function runCronjobs(): Response;
}
