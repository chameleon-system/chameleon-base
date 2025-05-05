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

use ChameleonSystem\MediaManager\MediaManagerListState;

class MediaManagerPickImagesMapper extends \AbstractViewMapper
{
    /**
     * {@inheritDoc}
     */
    public function GetRequirements(\IMapperRequirementsRestricted $oRequirements): void
    {
        $oRequirements->NeedsSourceObject('listState', MediaManagerListState::class);
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
         */
        $listState = $oVisitor->GetSourceObject('listState');
        if ($listState->isPickImageMode()) {
            $oVisitor->SetMappedValue('pickImageMode', true);
            $oVisitor->SetMappedValue(
                'pickImageCallback',
                $listState->getPickImageCallback()
            );
            $oVisitor->SetMappedValue(
                'parentIFrame',
                $listState->getParentIFrame()
            );
            $oVisitor->SetMappedValue(
                'pickImageWithCrop',
                $listState->isPickImageWithCrop()
            );
        }
    }
}
