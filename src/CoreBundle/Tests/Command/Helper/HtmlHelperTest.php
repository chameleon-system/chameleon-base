<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Tests\Command\Helper;

use ChameleonSystem\CoreBundle\Command\Helper\HtmlHelper;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\BufferedOutput;

class HtmlHelperTest extends TestCase
{
    /**
     * @test
     */
    public function itRendersTextUnaltered()
    {
        $output = new BufferedOutput();
        $helper = new HtmlHelper($output);
        $helper->render('foobar');

        $this->assertEquals("foobar\n", $output->fetch());
    }

    /**
     * @test
     */
    public function itConvertsBrToNewline()
    {
        $output = new BufferedOutput();
        $helper = new HtmlHelper($output);
        $helper->render('foo<br>bar<br />baz');

        $this->assertEquals("foo\nbar\nbaz\n", $output->fetch());
    }

    /**
     * @test
     */
    public function itTrimsWhitespace()
    {
        $output = new BufferedOutput();
        $helper = new HtmlHelper($output);
        $helper->render('foo<br>       bar     <br />              baz');

        $this->assertEquals("foo\n bar \n baz\n", $output->fetch());
    }

    /**
     * @test
     */
    public function itHighlightsHeaders()
    {
        $output = new BufferedOutput();
        $output->setDecorated(true);
        $helper = new HtmlHelper($output);
        $helper->render('foo<br><h1>bar</h1><br /><h2>baz</h2>');

        $this->assertEquals("foo\n[31;1mbar[39;22m\n\n[31;1mbaz[39;22m\n\n", $output->fetch());
    }

    /**
     * @test
     */
    public function itWritesANewLineAfterEveryHeaderAndResetsHeaderStyle()
    {
        $output = new BufferedOutput();
        $output->setDecorated(true);
        $helper = new HtmlHelper($output);
        $helper->render('foo<br><h1>bar</h1><h2>baz</h2>bazz');

        $this->assertEquals("foo\n[31;1mbar[39;22m\n[31;1mbaz[39;22m\nbazz\n", $output->fetch());
    }

    /**
     * @test
     */
    public function itThrowsAwayAllOtherTags()
    {
        $output = new BufferedOutput();
        $output->setDecorated(true);
        $helper = new HtmlHelper($output);
        $helper->render('foo<font class="deineMutterHatEineKlasse">bar</font><ul><li>baz</li></ul>');

        $this->assertEquals("foobarbaz\n", $output->fetch());
    }

    /**
     * @test
     */
    public function itHandlesAStdClass()
    {
        $output = new BufferedOutput();
        $output->setDecorated(true);
        $helper = new HtmlHelper($output);
        $stdClass = new \stdClass();
        $stdClass->message = 'foo';
        $stdClass->message2 = 'bar';
        $helper->render($stdClass);

        $this->assertEquals("foo\nbar\n", $output->fetch());
    }

    /**
     * @test
     */
    public function itHandlesAnArray()
    {
        $output = new BufferedOutput();
        $output->setDecorated(true);
        $helper = new HtmlHelper($output);
        $helper->render(['foo', 'bar']);

        $this->assertEquals("foo\nbar\n", $output->fetch());
    }
}
