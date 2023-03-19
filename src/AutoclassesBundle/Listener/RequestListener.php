<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\AutoclassesBundle\Listener;

use ChameleonSystem\AutoclassesBundle\CacheWarmer\AutoclassesCacheWarmer;
use ChameleonSystem\AutoclassesBundle\Exception\TPkgCmsCoreAutoClassManagerException_Recursion;
use ChameleonSystem\CoreBundle\Service\RequestInfoServiceInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class RequestListener
{

    private string $autoClassesDir;

    private AutoclassesCacheWarmer $cacheWarmer;

    private RequestInfoServiceInterface $requestInfoService;

    public function __construct(string $autoClassesDir, AutoclassesCacheWarmer $cacheWarmer, RequestInfoServiceInterface $requestInfoService)
    {
        $this->autoClassesDir = $autoClassesDir;
        $this->cacheWarmer = $cacheWarmer;
        $this->requestInfoService = $requestInfoService;
    }

    /**
     * @throws TPkgCmsCoreAutoClassManagerException_Recursion
     */
    public function checkAutoClasses(RequestEvent $evt): void
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $evt->getRequestType()) {
            return;
        }

        if (false === $this->requestInfoService->isBackendMode()) {
            return;
        }

        if (false === file_exists($this->autoClassesDir)) {
            $this->cacheWarmer->updateAllTables();
        }
    }
}
