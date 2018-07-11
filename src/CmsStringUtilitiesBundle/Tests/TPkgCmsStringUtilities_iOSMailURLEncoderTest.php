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

    protected function setUp()
    {
        parent::setUp();
        $this->util = new \TPkgCmsStringUtilities_iOSMailURLEncoder();
    }

    protected function tearDown()
    {
        parent::tearDown();
        $this->util = null;
    }

    /**
     * @test
     */
    public function it_leaves_normal_urls_alone()
    {
        $source = 'foo http://bar.bz?foo=bar baz';
        $this->assertEquals($source, $this->util->encode($source));
    }

    /**
     * @test
     */
    public function it_fixes_image_urls()
    {
        $source = 'foo <img src="http://bar.bz?foo=bar" /> bar';
        $expected = 'foo <img src="http://bar.bz?foo&#61;bar" /> bar';
        $this->assertEquals($expected, $this->util->encode($source));
    }

    /**
     * @test
     */
    public function it_fixes_image_urls_with_single_quotes()
    {
        $source = "foo <img src='http://bar.bz?foo=bar' /> bar";
        $expected = "foo <img src='http://bar.bz?foo&#61;bar' /> bar";
        $this->assertEquals($expected, $this->util->encode($source));
    }

    /**
     * @test
     */
    public function it_fixes_multiple_parameters()
    {
        $source = 'foo <img src="http://bar.bz?foo=bar&bar=baz" /> bar';
        $expected = 'foo <img src="http://bar.bz?foo&#61;bar&bar&#61;baz" /> bar';
        $this->assertEquals($expected, $this->util->encode($source));
    }

    /**
     * @test
     */
    public function it_fixes_multiple_tags()
    {
        $source = 'foo <img src="http://bar.bz?foo=bar" /> bar <img src="http://bar.bz?foo=baz" />';
        $expected = 'foo <img src="http://bar.bz?foo&#61;bar" /> bar <img src="http://bar.bz?foo&#61;baz" />';
        $this->assertEquals($expected, $this->util->encode($source));
    }

    /**
     * @test
     */
    public function it_leaves_anchors_alone()
    {
        $source = 'foo <img src="http://bar.bz?foo=bar" /> bar <a href="http://bar.bz?foo=baz" />';
        $expected = 'foo <img src="http://bar.bz?foo&#61;bar" /> bar <a href="http://bar.bz?foo=baz" />';
        $this->assertEquals($expected, $this->util->encode($source));
    }
}
