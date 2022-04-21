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

use AbstractViewMapper;
use ChameleonSystem\MediaManager\Exception\DataAccessException;
use ChameleonSystem\MediaManager\Exception\UsageFinderException;
use ChameleonSystem\MediaManager\Interfaces\MediaItemDataAccessInterface;
use ChameleonSystem\MediaManager\MediaItemChainUsageFinder;
use ChameleonSystem\MediaManager\MediaManagerListState;
use IMapperCacheTriggerRestricted;
use IMapperRequirementsRestricted;
use IMapperVisitorRestricted;
use Symfony\Contracts\Translation\TranslatorInterface;
use TdbCmsLanguage;

class MediaManagerConfirmDeleteMapper extends AbstractViewMapper
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

    /**
     * @param MediaItemChainUsageFinder    $mediaItemChainUsageFinder
     * @param MediaItemDataAccessInterface $mediaItemDataAccess
     * @param TranslatorInterface          $translator
     */
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
    public function GetRequirements(IMapperRequirementsRestricted $oRequirements)
    {
        $oRequirements->NeedsSourceObject('mediaItemIds', 'array');
        $oRequirements->NeedsSourceObject('listState', MediaManagerListState::class);
        $oRequirements->NeedsSourceObject('language', TdbCmsLanguage::class);
    }

    /**
     * {@inheritDoc}
     */
    public function Accept(
        IMapperVisitorRestricted $oVisitor,
        $bCachingEnabled,
        IMapperCacheTriggerRestricted $oCacheTriggerManager
    ) {
        /**
         * @var $listState    MediaManagerListState
         * @var $mediaItemIds string[]
         * @var $language     TdbCmsLanguage
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

        $mediaItemsWithUsages = array();
        foreach ($mediaItems as $mediaItem) {
            $usages = array();
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
            $mediaItemsWithUsages[] = array(
                'usages' => $usages,
                'mediaItem' => $mediaItem,
            );
        }

        $oVisitor->SetMappedValue('mediaItems', $mediaItemsWithUsages);
        $oVisitor->SetMappedValue('enableUsageSearch', $enableUsageSearch);
    }
}
