<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use PHPUnit\Framework\TestCase;

class TPkgCmsStringUtilities_HTMLTest extends TestCase
{
    /** @var TPkgCmsStringUtilities_HTML */
    protected $util;

    protected function setUp(): void
    {
        $this->util = new TPkgCmsStringUtilities_HTML();
        parent::setUp();
    }

    protected function tearDown(): void
    {
        $this->util = null;
        parent::tearDown();
    }

    public function testUnchanged()
    {
        $input = 'abcABC';
        $this->assertEquals('abcABC', $this->util->convertEntitiesWithBlacklist($input));
    }

    public function testAllUnchanged()
    {
        $input = '<>öÖ';
        $this->assertEquals('&lt;&gt;&ouml;&Ouml;', $this->util->convertEntitiesWithBlacklist($input));
    }

    public function testAllBlacklisted()
    {
        $input = '<>öÖ';
        $this->assertEquals('<>öÖ', $this->util->convertEntitiesWithBlacklist($input, ['<', '>', 'ö', 'Ö']));
    }

    public function testSomeBlacklisted()
    {
        $input = '<>öÖ';
        $this->assertEquals('<&gt;&ouml;Ö', $this->util->convertEntitiesWithBlacklist($input, ['<', 'Ö']));
    }
}
