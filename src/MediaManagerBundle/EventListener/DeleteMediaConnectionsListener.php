<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\MediaManagerBundle\EventListener;

use ChameleonSystem\CmsBackendBundle\BackendSession\BackendSessionInterface;
use ChameleonSystem\CoreBundle\Event\DeleteMediaEvent;
use ChameleonSystem\CoreBundle\Service\LanguageServiceInterface;
use ChameleonSystem\MediaManager\Exception\DataAccessException;
use ChameleonSystem\MediaManager\Exception\UsageDeleteException;
use ChameleonSystem\MediaManager\Exception\UsageFinderException;
use ChameleonSystem\MediaManager\Interfaces\MediaItemDataAccessInterface;
use ChameleonSystem\MediaManager\MediaItemChainUsageFinder;
use ChameleonSystem\MediaManager\MediaItemUsageChainDeleteService;
use Psr\Log\LoggerInterface;

class DeleteMediaConnectionsListener
{
    /**
     * @var bool
     */
    private $deleteReferences;

    /**
     * @var MediaItemChainUsageFinder
     */
    private $mediaItemChainUsageFinder;

    /**
     * @var MediaItemDataAccessInterface
     */
    private $mediaItemDataAccess;

    /**
     * @var MediaItemUsageChainDeleteService
     */
    private $mediaItemUsageChainDeleteService;

    /**
     * @var LanguageServiceInterface
     */
    private $languageService;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param bool $deleteReferences
     */
    public function __construct(
        $deleteReferences,
        MediaItemChainUsageFinder $mediaItemChainUsageFinder,
        MediaItemDataAccessInterface $mediaItemDataAccess,
        MediaItemUsageChainDeleteService $mediaItemUsageChainDeleteService,
        LanguageServiceInterface $languageService,
        LoggerInterface $logger,
        readonly private BackendSessionInterface $backendSession
    ) {
        $this->deleteReferences = $deleteReferences;
        $this->mediaItemChainUsageFinder = $mediaItemChainUsageFinder;
        $this->mediaItemDataAccess = $mediaItemDataAccess;
        $this->mediaItemUsageChainDeleteService = $mediaItemUsageChainDeleteService;
        $this->languageService = $languageService;
        $this->logger = $logger;
    }

    /**
     * @return void
     */
    public function onDeleteMedia(DeleteMediaEvent $deleteMediaEvent)
    {
        if ($this->shouldReferencesInUsagesBeDeletedOnMediaDelete()) {
            try {
                $usages = $this->mediaItemChainUsageFinder->findUsages(
                    $this->mediaItemDataAccess->getMediaItem(
                        $deleteMediaEvent->getDeletedMediaId(),
                        $this->backendSession->getCurrentEditLanguageId()
                    )
                );
                $this->mediaItemUsageChainDeleteService->deleteUsages($usages);
            } catch (UsageFinderException $e) {
                $this->logger->error(
                    sprintf(
                        'Could not find usages on delete of media item with ID %s: %s.',
                        $deleteMediaEvent->getDeletedMediaId(),
                        $e->getMessage()
                    )
                );
            } catch (UsageDeleteException $e) {
                $this->logger->error(
                    sprintf(
                        'Could not delete usages on delete of media item with ID %s: %s.',
                        $deleteMediaEvent->getDeletedMediaId(),
                        $e->getMessage()
                    )
                );
            } catch (DataAccessException $e) {
                $this->logger->error(
                    sprintf(
                        'Could not find media item with ID %s: %s.',
                        $deleteMediaEvent->getDeletedMediaId(),
                        $e->getMessage()
                    )
                );
            }
        }
    }

    /**
     * @return bool
     */
    private function shouldReferencesInUsagesBeDeletedOnMediaDelete()
    {
        return $this->deleteReferences;
    }
}
