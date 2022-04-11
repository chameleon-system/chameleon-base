<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\core\DatabaseAccessLayer;

class EntityListPager implements EntityListPagerInterface
{
    private $pageSize;

    public function __construct($pageSize)
    {
        $this->pageSize = $pageSize;
    }

    public function getQueryForPage($query, $pageNumberStartingAtZero)
    {
        if ($this->pageSize > 0) {
            $startRecord = $pageNumberStartingAtZero * $this->pageSize;

            return $this->removeLimitFromQuery($query)." LIMIT {$startRecord}, {$this->pageSize}";
        }

        return $this->removeLimitFromQuery($query);
    }

    /**
     * @param string $query
     * @return string
     */
    private function removeLimitFromQuery($query)
    {
        $normalizeQuery = $this->removeAllLineFeedsAndTabsFromQuery($query);
        $iLimitPos = strripos($normalizeQuery, ' LIMIT ');
        if (false === $iLimitPos) {
            return $query;
        }

        return substr($query, 0, $iLimitPos);
    }

    private function removeAllLineFeedsAndTabsFromQuery($query)
    {
        return str_replace(array("\n", "\r", "\t"), ' ', $query);
    }
}
