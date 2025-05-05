<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\DistributionBundle\Tests;

use ChameleonSystem\DistributionBundle\VersionCheck\Version\ChameleonVersion;
use ChameleonSystem\DistributionBundle\VersionCheck\Version\MatchLevel;
use PHPUnit\Framework\TestCase;

class ChameleonVersionTest extends TestCase
{
    /**
     * @test
     */
    public function itKnowsItsVersionAndName()
    {
        $version = new ChameleonVersion('foo', '1.0.0');
        $this->assertEquals('1.0.0', $version->getPrettyVersion());
        $this->assertEquals('foo', $version->getName());
    }

    /**
     * @test
     */
    public function itCanDecideIfItIsADevVersion()
    {
        $devVersions = [
            '6.0.x-dev',
            'dev-master',
            'dev-maintenance/1.0',
        ];

        foreach ($devVersions as $devVersion) {
            $version = new ChameleonVersion('foo', $devVersion);
            $this->assertTrue($version->isDev());
        }

        $stableVersions = [
            '6.0.x',
            '1.0.0',
            '5.0.3',
        ];

        foreach ($stableVersions as $stableVersion) {
            $version = new ChameleonVersion('foo', $stableVersion);
            $this->assertFalse($version->isDev());
        }
    }

    /**
     * @test
     */
    public function itFindsMismatches()
    {
        $candidates = [
            ['1.0.0', '1.0.0', MatchLevel::$MATCH_SAME, []],
            ['1.0.0', '2.0.0', MatchLevel::$MATCH_LEVEL_2, [MatchLevel::$MISSMATCH_TYPE_MAYOR_DIFF]],
            ['1.0.0', '1.1.0', MatchLevel::$MATCH_LEVEL_1, [MatchLevel::$MISSMATCH_TYPE_MINOR_DIFF]],
            ['1.0.0-dev', '1.0.0', MatchLevel::$MATCH_LEVEL_2, [MatchLevel::$MISSMATCH_TYPE_DEV_STABLE]],
            ['1.0.0-dev', '1.1.0', MatchLevel::$MATCH_LEVEL_2, [MatchLevel::$MISSMATCH_TYPE_DEV_STABLE, MatchLevel::$MISSMATCH_TYPE_MINOR_DIFF]],
            ['1.0.0-dev', '2.0.0', MatchLevel::$MATCH_LEVEL_2, [MatchLevel::$MISSMATCH_TYPE_DEV_STABLE, MatchLevel::$MISSMATCH_TYPE_MAYOR_DIFF]],
        ];

        foreach ($candidates as $candidate) {
            $version1 = new ChameleonVersion('project1', $candidate[0]);
            $version2 = new ChameleonVersion('project2', $candidate[1]);

            $matchLevel = $version1->match($version2);
            $this->assertEquals($candidate[2], $matchLevel->getMatchLevel());
            $this->assertEquals($candidate[3], $matchLevel->getMissmatchList());
        }
    }
}
