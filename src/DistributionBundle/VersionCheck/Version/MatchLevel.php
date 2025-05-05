<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\DistributionBundle\VersionCheck\Version;

class MatchLevel
{
    /**
     * @var int
     */
    public static $MATCH_SAME = 0;

    /**
     * @var int
     */
    public static $MATCH_LEVEL_1 = 1;

    /**
     * @var int
     */
    public static $MATCH_LEVEL_2 = 2;

    /**
     * @var int
     */
    public static $MISSMATCH_TYPE_DEV_STABLE = 0;

    /**
     * @var int
     */
    public static $MISSMATCH_TYPE_MINOR_DIFF = 1;

    /**
     * @var int
     */
    public static $MISSMATCH_TYPE_MAYOR_DIFF = 2;

    /**
     * @var int
     */
    private $matchLevel;

    /**
     * @var array
     */
    private $missmatchlist;

    /**
     * @param int $matchLevel
     */
    public function __construct($matchLevel, array $missmatchlist)
    {
        $this->matchLevel = $matchLevel;
        $this->missmatchlist = $missmatchlist;
    }

    /**
     * @return int
     */
    public function getMatchLevel()
    {
        return $this->matchLevel;
    }

    /**
     * @return int[]
     */
    public function getMissmatchList()
    {
        return $this->missmatchlist;
    }
}
