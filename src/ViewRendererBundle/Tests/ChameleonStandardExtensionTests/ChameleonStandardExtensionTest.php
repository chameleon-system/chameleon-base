<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\ViewRendererBundle\Tests\ChameleonStandardExtensionTests;

use ChameleonSystem\ViewRendererBundle\Twig\Extension\ChameleonStandardExtension;
use PHPUnit\Framework\TestCase;
use Twig_Environment;
use Twig_Markup;

class ChameleonStandardExtensionTest extends TestCase
{
    /**
     * @var Twig_Environment
     */
    private $env;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();
        $this->env = new Twig_Environment();
    }

    /**
     * @dataProvider getStringsForHtmlAttrEscape
     */
    public function testHtmlAttrEscape($input, $expectedOutput)
    {
        $actualOutput = ChameleonStandardExtension::chameleonTwigEscapeFilter($this->env, $input, 'html_attr');

        $this->assertEquals($expectedOutput, $actualOutput);
    }

    /**
     * @dataProvider getStringsForHtmlEscape
     */
    public function testHtmlEscape($input, $expectedOutput)
    {
        $actualOutput = ChameleonStandardExtension::chameleonTwigEscapeFilter($this->env, $input, 'html');

        $this->assertEquals($expectedOutput, $actualOutput);
    }

    /**
     * @dataProvider getStringsForNonStrings
     */
    public function testNonStrings($input)
    {
        $output = ChameleonStandardExtension::chameleonTwigEscapeFilter($this->env, $input);

        $this->assertEquals($input, $output);
    }

    public function getStringsForHtmlAttrEscape()
    {
        return [
            ['foo', 'foo'],
            ['http://foo.bar?cmsauthenticitytoken=[{cmsauthenticitytoken}]', 'http&#x3A;&#x2F;&#x2F;foo.bar&#x3F;cmsauthenticitytoken&#x3D;[{cmsauthenticitytoken}]'],
            ['http://foo.bar?foo=[{bar}]&cmsauthenticitytoken=[{cmsauthenticitytoken}]', 'http&#x3A;&#x2F;&#x2F;foo.bar&#x3F;foo&#x3D;&#x5B;&#x7B;bar&#x7D;&#x5D;&amp;cmsauthenticitytoken&#x3D;[{cmsauthenticitytoken}]'],
        ];
    }

    public function getStringsForHtmlEscape()
    {
        return [
            ['foo', 'foo'],
            ['http://foo.bar?cmsauthenticitytoken=[{cmsauthenticitytoken}]', 'http://foo.bar?cmsauthenticitytoken=[{cmsauthenticitytoken}]'],
            ['http://foo.bar?foo=[{bar}]&cmsauthenticitytoken=[{cmsauthenticitytoken}]', 'http://foo.bar?foo=[{bar}]&amp;cmsauthenticitytoken=[{cmsauthenticitytoken}]'],
        ];
    }

    public function getStringsForNonStrings()
    {
        return [
            [1],
            [0.4],
            [new Twig_Markup('foo', 'UTF-8')],
        ];
    }
}
