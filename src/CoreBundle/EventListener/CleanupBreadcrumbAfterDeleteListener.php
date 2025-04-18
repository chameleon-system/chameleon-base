<?php

namespace ChameleonSystem\CoreBundle\EventListener;

use ChameleonSystem\CoreBundle\Event\RecordChangeEvent;
use ChameleonSystem\CoreBundle\Service\BackendBreadcrumbServiceInterface;

class CleanupBreadcrumbAfterDeleteListener
{
    public function __construct(
        private readonly BackendBreadcrumbServiceInterface $breadcrumbService,
    ) {
    }

    public function onRecordDelete(RecordChangeEvent $event): void
    {
        $breadcrumb = $this->breadcrumbService->getBreadcrumb();
        if (null === $breadcrumb || [] === $breadcrumb->aHistory) {
            return;
        }

        $breadcrumb->removeEntries($event->getTableName(), $event->getRecordId());
    }
}
