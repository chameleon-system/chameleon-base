<?php

namespace ChameleonSystem\SecurityBundle\EventListener;

use ChameleonSystem\UpdateCounterMigrationBundle\Exception\InvalidMigrationCounterException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

class RedirectOnPendingUpdatesEventListener
{
    public function __construct(readonly private LoggerInterface $logger)
    {
    }

    public function onLoginSuccess(LoginSuccessEvent $event): void
    {
        $url = PATH_CMS_CONTROLLER.'?'.http_build_query(['pagedef' => 'CMSUpdateManager', 'module_fnc' => ['contentmodule' => 'RunUpdates']]);

        try {
            $allUpdateFilesToProcess = \TCMSUpdateManager::GetInstance()->getAllUpdateFilesToProcess();
            $numberOfUpdates = \count($allUpdateFilesToProcess);
            if ($numberOfUpdates > 0) {
                $this->logger->info(sprintf('Post login success redirect to update manager because there are %d updates', $numberOfUpdates), ['updates' => $allUpdateFilesToProcess]);
                $event->setResponse(new RedirectResponse($url));
            }
        } catch (InvalidMigrationCounterException $e) {
            $this->logger->error('Error checking pending updates post login success', ['exception' => $e]);
            $event->setResponse(new RedirectResponse($url));
        }
    }
}
