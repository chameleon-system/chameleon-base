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

/**
 * Interface representing sort columns in media manager.
 */
interface SortColumnInterface
{
    public const DIRECTION_DESCENDING = 'desc';

    public const DIRECTION_ASCENDING = 'asc';

    /**
     * @return string
     */
    public function getSortDirection();

    /**
     * @return string
     */
    public function getColumnName();

    /**
     * @return string
     */
    public function getSystemName();
}
