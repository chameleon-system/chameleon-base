<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\MediaManager\Interfaces;

use ChameleonSystem\MediaManager\DataModel\MediaItemDataModel;
use ChameleonSystem\MediaManager\DataModel\MediaItemUsageDataModel;
use ChameleonSystem\MediaManager\Exception\UsageFinderException;

/**
 * Find usages of media items in tables/records.
 */
interface MediaItemUsageFinderInterface
{
    /**
     * @return MediaItemUsageDataModel[]
     *
     * @throws UsageFinderException
     */
    public function findUsages(MediaItemDataModel $mediaItem);
}
