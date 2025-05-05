<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\ShopBundle\Tests;

use PHPUnit\Framework\TestCase;

class TPkgCmsStringUtilities_iOSMailURLEncoderTest extends TestCase
{
    /**
     * @var \TPkgCmsStringUtilities_iOSMailURLEncoder
     */
    private $util;

    protected function setUp(): void
    {
        parent::setUp();
        $this->util = new \TPkgCmsStringUtilities_iOSMailURLEncoder();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->util = null;
    }

    /**
     * @test
     */
    public function itLeavesNormalUrlsAlone()
    {
        $source = 'foo http://bar.bz?foo=bar baz';
        $this->assertEquals($source, $this->util->encode($source));
    }

    /**
     * @test
     */
    public function itFixesImageUrls()
    {
        $source = 'foo <img src="http://bar.bz?foo=bar" /> bar';
        $expected = 'foo <img src="http://bar.bz?foo&#61;bar" /> bar';
        $this->assertEquals($expected, $this->util->encode($source));
    }

    /**
     * @test
     */
    public function itFixesImageUrlsWithSingleQuotes()
    {
        $source = "foo <img src='http://bar.bz?foo=bar' /> bar";
        $expected = "foo <img src='http://bar.bz?foo&#61;bar' /> bar";
        $this->assertEquals($expected, $this->util->encode($source));
    }

    /**
     * @test
     */
    public function itFixesMultipleParameters()
    {
        $source = 'foo <img src="http://bar.bz?foo=bar&bar=baz" /> bar';
        $expected = 'foo <img src="http://bar.bz?foo&#61;bar&bar&#61;baz" /> bar';
        $this->assertEquals($expected, $this->util->encode($source));
    }

    /**
     * @test
     */
    public function itFixesMultipleTags()
    {
        $source = 'foo <img src="http://bar.bz?foo=bar" /> bar <img src="http://bar.bz?foo=baz" />';
        $expected = 'foo <img src="http://bar.bz?foo&#61;bar" /> bar <img src="http://bar.bz?foo&#61;baz" />';
        $this->assertEquals($expected, $this->util->encode($source));
    }

    /**
     * @test
     */
    public function itLeavesAnchorsAlone()
    {
        $source = 'foo <img src="http://bar.bz?foo=bar" /> bar <a href="http://bar.bz?foo=baz" />';
        $expected = 'foo <img src="http://bar.bz?foo&#61;bar" /> bar <a href="http://bar.bz?foo=baz" />';
        $this->assertEquals($expected, $this->util->encode($source));
    }
}
