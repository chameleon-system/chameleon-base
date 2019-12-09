<?php

namespace ChameleonSystem\CoreBundle\EventListener;

use ChameleonSystem\CoreBundle\Event\RecordChangeEvent;
use ChameleonSystem\CoreBundle\Service\BackendBreadcrumbServiceInterface;

class CleanupBreadcrumbAfterDeleteListener
{
    /**
     * @var BackendBreadcrumbServiceInterface
     */
    private $breadcrumbService;

    public function __construct(BackendBreadcrumbServiceInterface $breadcrumbService)
    {
        $this->breadcrumbService = $breadcrumbService;
    }

    public function onRecordDelete(RecordChangeEvent $event): void
    {
        $breadcrumb = $this->breadcrumbService->getBreadcrumb();

        if (null === $breadcrumb) {
            return;
        }

        $breadcrumb->removeEntries($event->getTableId(), $event->getRecordId());
    }
}
