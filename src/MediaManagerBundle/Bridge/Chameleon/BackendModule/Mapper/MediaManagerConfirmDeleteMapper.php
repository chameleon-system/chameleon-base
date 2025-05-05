<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\MediaManagerBundle\Bridge\Chameleon\BackendModule\Mapper;

use ChameleonSystem\MediaManager\Exception\DataAccessException;
use ChameleonSystem\MediaManager\Exception\UsageFinderException;
use ChameleonSystem\MediaManager\Interfaces\MediaItemDataAccessInterface;
use ChameleonSystem\MediaManager\MediaItemChainUsageFinder;
use ChameleonSystem\MediaManager\MediaManagerListState;
use Symfony\Contracts\Translation\TranslatorInterface;

class MediaManagerConfirmDeleteMapper extends \AbstractViewMapper
{
    /**
     * @var MediaItemChainUsageFinder
     */
    private $mediaItemChainUsageFinder;

    /**
     * @var MediaItemDataAccessInterface
     */
    private $mediaItemDataAccess;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(
        MediaItemChainUsageFinder $mediaItemChainUsageFinder,
        MediaItemDataAccessInterface $mediaItemDataAccess,
        TranslatorInterface $translator
    ) {
        $this->mediaItemChainUsageFinder = $mediaItemChainUsageFinder;
        $this->mediaItemDataAccess = $mediaItemDataAccess;
        $this->translator = $translator;
    }

    /**
     * {@inheritDoc}
     */
    public function GetRequirements(\IMapperRequirementsRestricted $oRequirements): void
    {
        $oRequirements->NeedsSourceObject('mediaItemIds', 'array');
        $oRequirements->NeedsSourceObject('listState', MediaManagerListState::class);
        $oRequirements->NeedsSourceObject('language', \TdbCmsLanguage::class);
    }

    /**
     * {@inheritDoc}
     */
    public function Accept(
        \IMapperVisitorRestricted $oVisitor,
        $bCachingEnabled,
        \IMapperCacheTriggerRestricted $oCacheTriggerManager
    ): void {
        /**
         * @var MediaManagerListState $listState
         * @var string[] $mediaItemIds
         * @var \TdbCmsLanguage $language
         */
        $listState = $oVisitor->GetSourceObject('listState');
        $enableUsageSearch = $listState->isDeleteWithUsageSearch();
        $mediaItemIds = $oVisitor->GetSourceObject('mediaItemIds');
        $language = $oVisitor->GetSourceObject('language');

        try {
            $mediaItems = $this->mediaItemDataAccess->getMediaItems($mediaItemIds, $language->id);
        } catch (DataAccessException $e) {
            $oVisitor->SetMappedValue(
                'errorMessage',
                $this->translator->trans('chameleon_system_media_manager.media_items.not_found_error_message')
            );
        }

        $mediaItemsWithUsages = [];
        foreach ($mediaItems as $mediaItem) {
            $usages = [];
            if (true === $enableUsageSearch) {
                try {
                    $usages = $this->mediaItemChainUsageFinder->findUsages($mediaItem);
                } catch (UsageFinderException $e) {
                    $oVisitor->SetMappedValue(
                        'usagesErrorMessage',
                        $this->translator->trans('chameleon_system_media_manager.usage_finder.error_finding_usages')
                    );
                }
            }
            $mediaItemsWithUsages[] = [
                'usages' => $usages,
                'mediaItem' => $mediaItem,
            ];
        }

        $oVisitor->SetMappedValue('mediaItems', $mediaItemsWithUsages);
        $oVisitor->SetMappedValue('enableUsageSearch', $enableUsageSearch);
    }
}
