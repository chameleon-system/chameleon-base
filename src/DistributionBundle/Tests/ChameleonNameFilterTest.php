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

use ChameleonSystem\DistributionBundle\VersionCheck\Filter\ChameleonPackageFilter;
use PHPUnit\Framework\TestCase;

class ChameleonNameFilterTest extends TestCase
{
    /**
     * @test
     */
    public function itAcceptsChameleonPackages()
    {
        $filter = new ChameleonPackageFilter();

        $test = [
            'chameleon-system/foo',
            'chameleon-system/bar',
            'chameleon-system/baz',
        ];

        foreach ($test as $name) {
            $this->assertTrue($filter->filter($name));
        }
    }

    /**
     * @test
     */
    public function itDeniesNonChameleonPackages()
    {
        $filter = new ChameleonPackageFilter();

        $test = [
            'Chameleon-system/foo',
            'chameleon-systemm/bar',
            'foo/bar',
            'chameleon-system/chameleon-base',
        ];

        foreach ($test as $name) {
            $this->assertFalse($filter->filter($name));
        }
    }
}
