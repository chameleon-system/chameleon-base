<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Tests\Util;

use ChameleonSystem\CoreBundle\DataModel\Routing\PagePath;
use ChameleonSystem\CoreBundle\Util\PageServiceUtil;
use ChameleonSystem\CoreBundle\Util\RoutingUtilInterface;
use ChameleonSystem\CoreBundle\Util\UrlUtil;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\DependencyInjection\Container;

class PageServiceUtilTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var PageServiceUtil
     */
    private $pageServiceUtil;
    /**
     * @var \TdbCmsLanguage|\PHPUnit_Framework_MockObject_MockObject
     */
    private $languageMock;
    /**
     * @var \TdbCmsPortal|\PHPUnit_Framework_MockObject_MockObject
     */
    private $portalMock;
    /**
     * @var \TdbCmsTplPage|\PHPUnit_Framework_MockObject_MockObject
     */
    private $pageMock;
    /**
     * @var UrlUtil|ObjectProphecy
     */
    private $urlUtilMock;
    /**
     * @var RoutingUtilInterface|ObjectProphecy
     */
    private $routingUtilMock;
    /**
     * @var Container|ObjectProphecy
     */
    private $containerMock;
    /**
     * @var string
     */
    private $actualResult;

    /**
     * @param string $persistedPath
     * @param bool $useTrailingSlashInsteadOfDotHtml
     * @param string $expectedPath
     *
     * @dataProvider getTestGetPagePathData
     */
    public function testGetPagePath($persistedPath, $useTrailingSlashInsteadOfDotHtml, $expectedPath)
    {
        $this->givenChameleonInfrastructure($useTrailingSlashInsteadOfDotHtml);
        $this->givenPersistedRoutes($persistedPath);
        $this->givenAPageServiceUtil(false);
        $this->whenICallGetPagePath();
        $this->thenIShouldGetTheExpectedPagePath($expectedPath);
    }

    /**
     * @param bool $useTrailingSlash
     */
    private function givenChameleonInfrastructure($useTrailingSlash)
    {
        $this->languageMock = $this->getMockBuilder('TdbCmsLanguage')->disableAutoload()->getMock();
        $this->portalMock = $this->getMockBuilder('TdbCmsPortal')->disableAutoload()->getMock();
        $this->portalMock->fieldUseSlashInSeoUrls = $useTrailingSlash;
        $this->getMockBuilder('TdbCmsTree')->disableAutoload()->getMock();
        $this->pageMock = $this->getMockBuilder('TdbCmsTplPage')->disableAutoload()->setMethods(['GetPortal'])->getMock();
        $this->pageMock->id = '42';
        $this->pageMock->method('GetPortal')->willReturn($this->portalMock);
        $this->urlUtilMock = $this->prophesize('ChameleonSystem\CoreBundle\Util\UrlUtil');
        $this->routingUtilMock = $this->prophesize('ChameleonSystem\CoreBundle\Util\RoutingUtilInterface');
        $this->containerMock = $this->prophesize('Symfony\Component\DependencyInjection\ContainerInterface');
        $this->containerMock->get('chameleon_system_core.util.routing')->willReturn($this->routingUtilMock);
    }

    /**
     * @param string $persistedPath
     */
    private function givenPersistedRoutes($persistedPath)
    {
        $this->routingUtilMock->getAllPageRoutes($this->portalMock, $this->languageMock)->willReturn([
            '42' => new PagePath('42', $persistedPath),
        ]);
    }

    /**
     * @param bool $removeTrailingSlash
     */
    private function givenAPageServiceUtil($removeTrailingSlash)
    {
        $this->pageServiceUtil = new PageServiceUtil(
            $this->urlUtilMock->reveal(),
            $this->containerMock->reveal(),
            $removeTrailingSlash
        );
    }

    private function whenICallGetPagePath()
    {
        $this->actualResult = $this->pageServiceUtil->getPagePath($this->pageMock, $this->languageMock);
    }

    /**
     * @param string $expectedResult
     */
    private function thenIShouldGetTheExpectedPagePath($expectedResult)
    {
        static::assertEquals($expectedResult, $this->actualResult);
    }

    /**
     * @return array
     */
    public function getTestGetPagePathData()
    {
        return [
            [
                'foo/bar',
                true,
                'foo/bar',
            ],
            [
                'foo/bar',
                false,
                'foo/bar.html',
            ],
            [
                '/',
                true,
                '',
            ],
            [
                '/',
                false,
                '',
            ],
            [
                '',
                true,
                '',
            ],
            [
                '',
                false,
                '',
            ],
        ];
    }

    /**
     * @param string $url
     * @param string $expectedUrl
     * @param bool $useTrailingSlashInsteadOfDotHtml
     * @param bool $removeTrailingSlash
     *
     * @dataProvider getTestPostProcessUrlData
     */
    public function testPostProcessUrl($url, $expectedUrl, $useTrailingSlashInsteadOfDotHtml, $removeTrailingSlash, $forceSecure)
    {
        $this->givenChameleonInfrastructure($useTrailingSlashInsteadOfDotHtml);
        $this->givenAPageServiceUtil($removeTrailingSlash);
        $this->whenICallPostProcessUrl($url, $forceSecure);
        $this->thenIShouldGetTheCorrectlyProcessedUrl($expectedUrl);
    }

    /**
     * @param string $url
     * @param bool $forceSecure
     */
    private function whenICallPostProcessUrl($url, $forceSecure)
    {
        $this->urlUtilMock->isUrlSecure(Argument::any())->willReturn(false);
        $this->urlUtilMock->getAbsoluteUrl($url, true, null, $this->portalMock, $this->languageMock)->willReturn('https://example.com'.$url);

        $this->actualResult = $this->pageServiceUtil->postProcessUrl($url, $this->portalMock, $this->languageMock, $forceSecure);
    }

    /**
     * @param string $expectedResult
     */
    private function thenIShouldGetTheCorrectlyProcessedUrl($expectedResult)
    {
        static::assertEquals($expectedResult, $this->actualResult);
    }

    /**
     * @return array
     */
    public function getTestPostProcessUrlData()
    {
        return [
            [
                '/foo/',
                '/foo/',
                true,
                false,
                false,
            ],
            [
                '/foo/',
                '/foo',
                true,
                true,
                false,
            ],
            [
                '/foo/',
                '/foo',
                false,
                true,
                false,
            ],
            [
                '/foo',
                '/foo',
                false,
                true,
                false,
            ],
            [
                '/foo',
                '/foo',
                true,
                true,
                false,
            ],
            [
                '/foo',
                '/foo/',
                true,
                false,
                false,
            ],
            [
                '/foo?bar=baz',
                '/foo/?bar=baz',
                true,
                false,
                false,
            ],
            [
                '/foo/?bar=baz',
                '/foo/?bar=baz',
                true,
                false,
                false,
            ],
            [
                '/foo/bar?baz=quuz',
                '/foo/bar/?baz=quuz',
                true,
                false,
                false,
            ],
            [
                '/foo/bar/?baz=quuz',
                '/foo/bar/?baz=quuz',
                true,
                false,
                false,
            ],
            [
                '/foo/',
                'https://example.com/foo/',
                true,
                false,
                true,
            ],
            [
                '/foo',
                'https://example.com/foo',
                true,
                true,
                true,
            ],
            [
                '/foo',
                'https://example.com/foo',
                false,
                true,
                true,
            ],
        ];
    }
}
