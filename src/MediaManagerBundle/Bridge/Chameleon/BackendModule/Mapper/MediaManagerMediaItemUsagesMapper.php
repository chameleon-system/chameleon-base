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
use ChameleonSystem\MediaManager\DataModel\MediaItemDataModel;
use ChameleonSystem\MediaManager\Exception\UsageFinderException;
use ChameleonSystem\MediaManager\MediaItemChainUsageFinder;
use IMapperCacheTriggerRestricted;
use IMapperRequirementsRestricted;
use IMapperVisitorRestricted;
use Symfony\Contracts\Translation\TranslatorInterface;

class MediaManagerMediaItemUsagesMapper extends AbstractViewMapper
{
    /**
     * @var MediaItemChainUsageFinder
     */
    private $mediaItemChainUsageFinder;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param MediaItemChainUsageFinder $mediaItemChainUsageFinder
     * @param TranslatorInterface       $translator
     */
    public function __construct(MediaItemChainUsageFinder $mediaItemChainUsageFinder, TranslatorInterface $translator)
    {
        $this->mediaItemChainUsageFinder = $mediaItemChainUsageFinder;
        $this->translator = $translator;
    }

    /**
     * {@inheritDoc}
     */
    public function GetRequirements(IMapperRequirementsRestricted $oRequirements)
    {
        $oRequirements->NeedsSourceObject('mediaItem', MediaItemDataModel::class);
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
         * @var $mediaItem MediaItemDataModel
         */
        $mediaItem = $oVisitor->GetSourceObject('mediaItem');
        try {
            $allUsages = $this->mediaItemChainUsageFinder->findUsages($mediaItem);
            $oVisitor->SetMappedValue('usages', $allUsages);
        } catch (UsageFinderException $e) {
            $oVisitor->SetMappedValue(
                'usagesErrorMessage',
                $this->translator->trans('chameleon_system_media_manager.usage_finder.error_finding_usages')
            );
        }
    }
}
