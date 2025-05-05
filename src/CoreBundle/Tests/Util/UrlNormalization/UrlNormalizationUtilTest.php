<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Tests\Util\UrlNormalization;

use ChameleonSystem\CoreBundle\Util\UrlNormalization\UrlNormalizationUtil;
use ChameleonSystem\CoreBundle\Util\UrlNormalization\UrlNormalizerBasicEnd;
use ChameleonSystem\CoreBundle\Util\UrlNormalization\UrlNormalizerBasicStart;
use ChameleonSystem\CoreBundle\Util\UrlNormalization\UrlNormalizerGerman;
use ChameleonSystem\CoreBundle\Util\UrlNormalization\UrlNormalizerNonLatin;
use ChameleonSystem\CoreBundle\Util\UrlNormalization\UrlNormalizerRomanian;
use ChameleonSystem\CoreBundle\Util\UrlNormalization\UrlNormalizerSlavic;
use ChameleonSystem\CoreBundle\Util\UrlNormalization\UrlNormalizerSpecialChars;
use PHPUnit\Framework\TestCase;

class UrlNormalizationUtilTest extends TestCase
{
    /**
     * @var UrlNormalizationUtil
     */
    private $urlNormalizationUtil;
    /**
     * @var string
     */
    private $actualResult;

    protected function setUp(): void
    {
        parent::setUp();
        $this->urlNormalizationUtil = null;
        $this->actualResult = null;
    }

    /**
     * @test
     *
     * @dataProvider getData
     */
    public function itShouldReturnCorrectlyNormalizedUrls($url, $spacer, $expected)
    {
        $this->givenAUrlNormalizationUtil();
        $this->whenNormalizeUrlIsCalled($url, $spacer);
        $this->thenItShouldReturnCorrectlyNormalizedUrls($expected);
    }

    /**
     * This method should add all normalizers that are currently used in Chameleon. Add newly implemented normalizers.
     */
    private function givenAUrlNormalizationUtil()
    {
        $this->urlNormalizationUtil = new UrlNormalizationUtil();
        $this->urlNormalizationUtil->addNormalizer(new UrlNormalizerBasicStart());
        $this->urlNormalizationUtil->addNormalizer(new UrlNormalizerGerman());
        $this->urlNormalizationUtil->addNormalizer(new UrlNormalizerSlavic());
        $this->urlNormalizationUtil->addNormalizer(new UrlNormalizerRomanian());
        $this->urlNormalizationUtil->addNormalizer(new UrlNormalizerSpecialChars());
        $this->urlNormalizationUtil->addNormalizer(new UrlNormalizerNonLatin());
        $this->urlNormalizationUtil->addNormalizer(new UrlNormalizerBasicEnd());
    }

    /**
     * @param string $url
     * @param string $spacer
     */
    private function whenNormalizeUrlIsCalled($url, $spacer)
    {
        $this->actualResult = $this->urlNormalizationUtil->normalizeUrl($url, $spacer);
    }

    /**
     * @param string $expected
     */
    private function thenItShouldReturnCorrectlyNormalizedUrls($expected)
    {
        $this->assertEquals($expected, $this->actualResult);
    }

    /**
     * @return array
     */
    public function getData()
    {
        return [
            [
                'this is a test',
                '-',
                'this-is-a-test',
            ],
            [
                'Chameleon "Professional"',
                '-',
                'Chameleon-Professional',
            ],
            [
                '  wöw such şÞêÇîäł characters!',
                '-',
                'woew-such-sBeCiael-characters',
            ],
            [
                '¹²³',
                '-',
                '',
            ],
            [
                '-¹²³',
                '-',
                '-',
            ],
            [
                '',
                '-',
                '',
            ],
            [
                '////////////////',
                '-',
                '-',
            ],
            [
                '-----',
                '-',
                '-',
            ],
            [
                '',
                '',
                '',
            ],
            [
                '<h1>many HTML</h1>',
                '-',
                'h1many-HTML-h1',
            ],
            [
                '联系',
                '-',
                '%E8%81%94%E7%B3%BB',
            ],
            [
                '联系-such-chinese',
                '-',
                '%E8%81%94%E7%B3%BB-such-chinese',
            ],
            [
                '€$¥₴',
                '-',
                '-EUR-USDYEN', // converter adds spaces before EUR and USD which are then converted to a spacer char
            ],
            [
                '%E8%81%94%E7%B3%BB',
                '-',
                '%E8%81%94%E7%B3%BB', // converter adds spaces before EUR and USD which are then converted to a spacer char
            ],
        ];
    }
}
