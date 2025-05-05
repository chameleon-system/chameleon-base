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

use ChameleonSystem\MediaManager\MediaManagerListRequest;
use ChameleonSystem\MediaManager\MediaManagerListState;

/**
 * Create a list request for media manager.
 */
interface MediaManagerListRequestFactoryInterface
{
    /**
     * @param string $languageId
     *
     * @return MediaManagerListRequest
     */
    public function createListRequestFromListState(MediaManagerListState $listState, $languageId);
}
