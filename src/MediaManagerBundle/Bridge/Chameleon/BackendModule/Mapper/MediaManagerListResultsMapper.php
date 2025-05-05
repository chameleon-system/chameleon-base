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
use ChameleonSystem\MediaManager\Interfaces\MediaItemDataAccessInterface;
use ChameleonSystem\MediaManager\Interfaces\MediaManagerListRequestFactoryInterface;
use ChameleonSystem\MediaManager\MediaManagerListState;

class MediaManagerListResultsMapper extends \AbstractViewMapper
{
    /**
     * @var MediaManagerListRequestFactoryInterface
     */
    private $mediaManagerListRequestService;

    /**
     * @var MediaItemDataAccessInterface
     */
    private $mediaItemDataAccess;

    public function __construct(
        MediaManagerListRequestFactoryInterface $mediaManagerListRequestService,
        MediaItemDataAccessInterface $mediaItemDataAccess
    ) {
        $this->mediaManagerListRequestService = $mediaManagerListRequestService;
        $this->mediaItemDataAccess = $mediaItemDataAccess;
    }

    /**
     * {@inheritDoc}
     */
    public function GetRequirements(\IMapperRequirementsRestricted $oRequirements): void
    {
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
         * @var \TdbCmsLanguage $language
         */
        $listState = $oVisitor->GetSourceObject('listState');
        $language = $oVisitor->GetSourceObject('language');
        try {
            $listRequest = $this->mediaManagerListRequestService->createListRequestFromListState(
                $listState,
                $language->id
            );
            $result = $this->mediaItemDataAccess->getMediaItemList($listRequest, $language->id);
        } catch (DataAccessException $e) {
            throw new \MapperException(
                sprintf('Error getting media manager list result: %s', $e->getMessage()),
                $e->getCode(),
                $e
            );
        }
        $oVisitor->SetMappedValue('listResult', $result);
    }
}
