<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\MediaManager;

use ChameleonSystem\MediaManager\DataModel\MediaItemUsageDataModel;
use ChameleonSystem\MediaManager\Exception\UsageDeleteException;
use ChameleonSystem\MediaManager\Interfaces\MediaItemUsageDeleteServiceInterface;

class MediaItemUsageChainDeleteService
{
    /**
     * @var MediaItemUsageDeleteServiceInterface[]
     */
    private $mediaItemDeleteServices = array();

    /**
     * @return void
     */
    public function addUsageDeleteService(MediaItemUsageDeleteServiceInterface $mediaItemDeleteService)
    {
        $this->mediaItemDeleteServices[] = $mediaItemDeleteService;
    }

    /**
     * @param MediaItemUsageDataModel[] $usages
     *
     * @throws UsageDeleteException
     *
     * @return void
     */
    public function deleteUsages(array $usages)
    {
        foreach ($usages as $usage) {
            $this->deleteUsage($usage);
        }
    }

    /**
     * @param MediaItemUsageDataModel $usage
     *
     * @throws UsageDeleteException
     *
     * @return void
     */
    public function deleteUsage(MediaItemUsageDataModel $usage)
    {
        foreach ($this->mediaItemDeleteServices as $deleteService) {
            if (true === $deleteService->deleteUsage($usage)) {
                break;
            }
        }
    }
}
