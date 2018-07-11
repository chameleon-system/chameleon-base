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

use ChameleonSystem\MediaManager\MediaManagerListState;

/**
 * Provide the current list state in media manager.
 */
interface MediaManagerListStateServiceInterface
{
    /**
     * @return MediaManagerListState
     */
    public function getListState();
}
