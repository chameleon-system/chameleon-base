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

use ChameleonSystem\MediaManager\DataModel\MediaItemUsageDataModel;
use ChameleonSystem\MediaManager\Exception\UsageDeleteException;

/**
 * Delete references to media items when they are deleted.
 */
interface MediaItemUsageDeleteServiceInterface
{
    /**
     * @return bool - return true if usage was deleted
     *
     * @throws UsageDeleteException
     */
    public function deleteUsage(MediaItemUsageDataModel $usage);
}
