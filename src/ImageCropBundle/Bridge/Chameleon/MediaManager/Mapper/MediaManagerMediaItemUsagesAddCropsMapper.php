<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\ImageCropBundle\Bridge\Chameleon\MediaManager\Mapper;

use AbstractViewMapper;
use ChameleonSystem\ImageCrop\DataModel\ImageCropDataModel;
use ChameleonSystem\MediaManager\DataModel\MediaItemUsageDataModel;
use IMapperCacheTriggerRestricted;
use IMapperRequirementsRestricted;
use IMapperVisitorRestricted;

class MediaManagerMediaItemUsagesAddCropsMapper extends AbstractViewMapper
{
    /**
     * {@inheritDoc}
     */
    public function GetRequirements(IMapperRequirementsRestricted $oRequirements)
    {
        $oRequirements->NeedsSourceObject('crops', 'array');
        $oRequirements->NeedsSourceObject('usages', 'array');
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
         * @var $crops  ImageCropDataModel[]
         * @var $usages MediaItemUsageDataModel[]
         */
        $crops = $oVisitor->GetSourceObject('crops');
        $allUsages = $oVisitor->GetSourceObject('usages');

        $cropIds = array();
        foreach ($crops as $crop) {
            $cropIds[$crop->getId()] = true;
        }

        $usagesByCrop = array();
        foreach ($allUsages as $usage) {
            $usageCropId = $usage->getCropId();
            if (null !== $usageCropId && '' !== $usageCropId && isset($cropIds[$usageCropId])) {
                if (false === isset($usagesByCrop[$usageCropId])) {
                    $usagesByCrop[$usageCropId] = array();
                }
                $usagesByCrop[$usageCropId][] = $usage;
            }
        }

        $oVisitor->SetMappedValue('usagesByCrop', $usagesByCrop);
    }
}
