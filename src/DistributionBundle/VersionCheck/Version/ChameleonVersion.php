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

class ChameleonVersion
{
    /**
     * @var string
     */
    private $prettyVersion;

    /**
     * @var string
     */
    private $name;

    /**
     * @param string $name
     * @param string $prettyVersion
     */
    public function __construct($name, $prettyVersion)
    {
        $this->prettyVersion = $prettyVersion;
        $this->name = $name;
    }

    /**
     * @return bool
     */
    public function isDev()
    {
        return
            '-dev' === substr($this->prettyVersion, -1 * strlen('-dev'))
            || 'dev-' === substr($this->prettyVersion, 0, strlen('dev-'));
    }

    /**
     * @return string
     */
    public function getMajorVersion()
    {
        return $this->getVersionPart(0);
    }

    /**
     * @return string
     */
    public function getMinorVersion()
    {
        return $this->getVersionPart(1);
    }

    /**
     * @param int $part
     *
     * @return string
     */
    private function getVersionPart($part)
    {
        $matches = [];
        if (preg_match(",(dev-)?(\d+\.\d+\.\d+)(-dev)?,", $this->getPrettyVersion(), $matches)) {
            $parts = explode('.', $matches[2]);

            return $parts[$part];
        }

        return $this->getPrettyVersion();
    }

    /**
     * @return MatchLevel
     */
    public function match(self $version)
    {
        if ($this->getPrettyVersion() === $version->getPrettyVersion()) {
            return new MatchLevel(MatchLevel::$MATCH_SAME, []);
        }

        $misMatches = [];
        $matchLevel = MatchLevel::$MATCH_SAME;
        if ($this->isDev() !== $version->isDev()) {
            $misMatches[] = MatchLevel::$MISSMATCH_TYPE_DEV_STABLE;
            $matchLevel = $matchLevel <= MatchLevel::$MATCH_LEVEL_2 ? MatchLevel::$MATCH_LEVEL_2 : $matchLevel;
        }

        if ($this->getMajorVersion() !== $version->getMajorVersion()) {
            $misMatches[] = MatchLevel::$MISSMATCH_TYPE_MAYOR_DIFF;
            $matchLevel = $matchLevel <= MatchLevel::$MATCH_LEVEL_2 ? MatchLevel::$MATCH_LEVEL_2 : $matchLevel;
        } elseif ($this->getMinorVersion() !== $version->getMinorVersion()) {
            $misMatches[] = MatchLevel::$MISSMATCH_TYPE_MINOR_DIFF;
            $matchLevel = $matchLevel <= MatchLevel::$MATCH_LEVEL_1 ? MatchLevel::$MATCH_LEVEL_1 : $matchLevel;
        }

        return new MatchLevel($matchLevel, $misMatches);
    }

    /**
     * @return string
     */
    public function getPrettyVersion()
    {
        return $this->prettyVersion;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
