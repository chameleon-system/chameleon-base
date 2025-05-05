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
use ChameleonSystem\CoreBundle\Service\RequestInfoServiceInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class RequestListener
{
    /**
     * @var string
     */
    private $autoclassesDir;
    /**
     * @var AutoclassesCacheWarmer
     */
    private $cacheWarmer;
    /**
     * @var RequestInfoServiceInterface
     */
    private $requestInfoService;

    /**
     * @param string $autoclassesDir
     */
    public function __construct($autoclassesDir, AutoclassesCacheWarmer $cacheWarmer, RequestInfoServiceInterface $requestInfoService)
    {
        $this->autoclassesDir = $autoclassesDir;
        $this->cacheWarmer = $cacheWarmer;
        $this->requestInfoService = $requestInfoService;
    }

    /**
     * @return void
     */
    public function checkAutoclasses(RequestEvent $evt)
    {
        if (HttpKernelInterface::MAIN_REQUEST !== $evt->getRequestType()) {
            return;
        }

        if (false === $this->requestInfoService->isBackendMode()) {
            return;
        }

        if (!file_exists($this->autoclassesDir)) {
            $this->cacheWarmer->updateAllTables();
        }
    }
}
