<?php

namespace ChameleonSystem\CoreBundle\Service;

use Doctrine\DBAL\Exception;
use Symfony\Component\HttpFoundation\Response;

interface CronJobServiceInterface
{
    /**
     * @throws Exception
     */
    public function runCronjobs(): Response;
}