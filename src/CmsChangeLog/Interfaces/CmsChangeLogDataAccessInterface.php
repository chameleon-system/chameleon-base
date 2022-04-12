<?php

namespace ChameleonSystem\CmsChangeLog\Interfaces;

use ChameleonSystem\CmsChangeLog\Exception\CmsChangeLogDataAccessFailedException;

interface CmsChangeLogDataAccessInterface
{
    /**
     * @param int $days
     * @return int - number of changed entries
     * @throws CmsChangeLogDataAccessFailedException
     */
    public function deleteOlderThan(int $days): int;
}
