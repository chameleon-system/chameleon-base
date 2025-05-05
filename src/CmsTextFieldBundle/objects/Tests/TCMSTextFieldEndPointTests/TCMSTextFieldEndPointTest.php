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

require dirname(__FILE__).'/../../TCMSTextFieldEndPoint.class.php';

class TCMSTextFieldEndPointTest extends TestCase
{
    /**
     * @var TCMSTextFieldEndPoint
     */
    protected $oCmsTextFieldEndPoint;

    /** @var ReflectionMethod */
    protected $_GetDownloadSpans;

    public function setUp(): void
    {
        $this->oCmsTextFieldEndPoint = new TCMSTextFieldEndPoint();
        $this->_GetDownloadSpans = new ReflectionMethod('TCMSTextFieldEndPoint', '_GetDownloadSpans');
        $this->_GetDownloadSpans->setAccessible(true);
    }

    public function TearDown(): void
    {
        $this->oCmsTextFieldEndPoint = null;
    }

    public function testNoSpansPresent()
    {
        $result = $this->_GetDownloadSpans->invoke($this->oCmsTextFieldEndPoint, 'foobar');

        $this->assertEquals([], $result);
    }

    public function testNoDownloadSpansPresent()
    {
        $result = $this->_GetDownloadSpans->invoke($this->oCmsTextFieldEndPoint, 'foobar<span>baz</span>bar');
        $this->assertEquals([], $result);
    }

    public function testOneDownloadSpanWithWrongAttributePresent()
    {
        $result = $this->_GetDownloadSpans->invoke($this->oCmsTextFieldEndPoint, 'foobar<span class="cmsdocument">baz</span>bar');
        $this->assertEquals([], $result);
    }

    public function testOneDownloadSpanPresent()
    {
        $result = $this->_GetDownloadSpans->invoke($this->oCmsTextFieldEndPoint, 'foobar<span class="cmsdocument_">baz</span>bar');
        $this->assertEquals(['<span class="cmsdocument_">baz</span>'], $result);
    }

    public function testTwoDownloadSpanPresent()
    {
        $result = $this->_GetDownloadSpans->invoke($this->oCmsTextFieldEndPoint, 'foobar<span class="cmsdocument_">baz</span>bar<span class="cmsdocument_">foo</span>baz');
        $this->assertEquals(['<span class="cmsdocument_">baz</span>', '<span class="cmsdocument_">foo</span>'], $result);
    }

    public function testNestedSpanPresent()
    {
        $result = $this->_GetDownloadSpans->invoke($this->oCmsTextFieldEndPoint, 'foobar<span class="cmsdocument_"><span>baz</span></span>bar');
        $this->assertEquals(['<span class="cmsdocument_"><span>baz</span></span>'], $result);
    }

    public function testNestedSpanWithAttributePresent()
    {
        $result = $this->_GetDownloadSpans->invoke($this->oCmsTextFieldEndPoint, 'foobar<span class="cmsdocument_"><span class="bar">baz</span></span>bar');
        $this->assertEquals(['<span class="cmsdocument_"><span class="bar">baz</span></span>'], $result);
    }

    public function testTwoNestedSpanPresent()
    {
        $result = $this->_GetDownloadSpans->invoke($this->oCmsTextFieldEndPoint, 'foobar<span class="cmsdocument_"><span><span>foo</span>baz</span></span>bar');
        $this->assertEquals(['<span class="cmsdocument_"><span><span>foo</span>baz</span></span>'], $result);
    }

    public function testUpperCaseDownloadSpanPresent()
    {
        $result = $this->_GetDownloadSpans->invoke($this->oCmsTextFieldEndPoint, 'foobar<SPAN class="cmsdocument_">baz</SPAN>bar');
        $this->assertEquals(['<SPAN class="cmsdocument_">baz</SPAN>'], $result);
    }

    public function testUnicodeCharsPresent()
    {
        $result = $this->_GetDownloadSpans->invoke($this->oCmsTextFieldEndPoint, '产品种类<SPAN class="cmsdocument_">品种类</SPAN>bar');
        $this->assertEquals(['<SPAN class="cmsdocument_">品种类</SPAN>'], $result);
    }

    public function testEmptyLink()
    {
        $result = $this->_GetDownloadSpans->invoke($this->oCmsTextFieldEndPoint, '产品种类<SPAN class="cmsdocument_"></SPAN>bar');
        $this->assertEquals(['<SPAN class="cmsdocument_"></SPAN>'], $result);
    }

    public function testEmptyNestedLink()
    {
        $result = $this->_GetDownloadSpans->invoke($this->oCmsTextFieldEndPoint, '产品种类<SPAN class="cmsdocument_"><span></span></SPAN>bar');
        $this->assertEquals(['<SPAN class="cmsdocument_"><span></span></SPAN>'], $result);
    }

    public function testNewLineLink()
    {
        $result = $this->_GetDownloadSpans->invoke($this->oCmsTextFieldEndPoint, "foo<SPAN class=\"cmsdocument_\">with\nnewline</SPAN>bar");
        $this->assertEquals(["<SPAN class=\"cmsdocument_\">with\nnewline</SPAN>"], $result);
    }

    public function testBrokenHTMLLink()
    {
        $result = $this->_GetDownloadSpans->invoke($this->oCmsTextFieldEndPoint, 'foo<SPAN class="cmsdocument_"><span></SPAN>bar<span class="cmsdocument_"></span>');
        $this->assertEquals(['<SPAN class="cmsdocument_"><span></SPAN>bar<span class="cmsdocument_"></span>'], $result);
    }

    public function testMassiveHTMLLink()
    {
        $sTestHTML = '';
        $aExpectedResult = [];
        for ($i = 0; $i < 200; ++$i) {
            $sTestHTML .= 'foobar<span class=\"cmsdocument_\">baz</span>bar';
            $aExpectedResult[] = '<span class=\"cmsdocument_\">baz</span>';
        }
        $result = $this->_GetDownloadSpans->invoke($this->oCmsTextFieldEndPoint, $sTestHTML);
        $this->assertEquals($aExpectedResult, $result);
    }

    public function testNeverendingHTMLLink()
    {
        $result = $this->_GetDownloadSpans->invoke($this->oCmsTextFieldEndPoint, 'foo<SPAN class="cmsdocument_"><span></SPAN>bar<span class="cmsdocument_"></span><span>');
        $this->assertEquals(['<SPAN class="cmsdocument_"><span></SPAN>bar<span class="cmsdocument_"></span><span>'], $result);
    }

    public function testNotClosedLinkAtEndPresent()
    {
        $result = $this->_GetDownloadSpans->invoke($this->oCmsTextFieldEndPoint, 'foobar<span class="cmsdocument_">baz</span>bar<span class="cmsdocument_">foo');
        $this->assertEquals(['<span class="cmsdocument_">baz</span>', '<span class="cmsdocument_">foo'], $result);
    }

    public function testReplaceMultipleWidthButNotBorderWidth()
    {
        $sSubject = 'width: 123px; margin: 20px 0px; width: 470px; height: 96px; border-width: 0px; border-style: solid;';
        $sExpectedResult = ' margin: 20px 0px; height: 96px; border-width: 0px; border-style: solid;';
        $oMyTextField = new TCMSTextFieldEndPoint();
        $reflectionMethod = new ReflectionMethod('TCMSTextFieldEndPoint', 'removeAllOccurences');
        $reflectionMethod->setAccessible(true);
        $sResult = $reflectionMethod->invokeArgs($oMyTextField, [$sSubject, 'width']);
        $this->assertEquals($sExpectedResult, $sResult);
    }
}
