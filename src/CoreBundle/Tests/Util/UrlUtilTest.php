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

use ChameleonSystem\CoreBundle\Routing\DomainValidatorInterface;
use ChameleonSystem\CoreBundle\Security\AuthenticityToken\AuthenticityTokenManagerInterface;
use ChameleonSystem\CoreBundle\Service\LanguageServiceInterface;
use ChameleonSystem\CoreBundle\Service\PortalDomainServiceInterface;
use ChameleonSystem\CoreBundle\Util\UrlPrefixGeneratorInterface;
use ChameleonSystem\CoreBundle\Util\UrlUtil;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\HttpFoundation\Request;

class UrlUtilTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var Request
     */
    private $request;
    /**
     * @var UrlUtil
     */
    private $urlUtil;
    /**
     * @var string
     */
    private $transformedUrl;

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        $this->request = null;
        $this->urlUtil = null;
        $this->transformedUrl = null;
    }

    /**
     * @dataProvider getDataForTestTransformHttpUrlToHttpsUrl
     *
     * @param string $sourceUrl
     * @param string $expectedUrl
     * @param string $protocol
     */
    public function testTransformHttpUrlToHttpsUrl($sourceUrl, $expectedUrl, $protocol, array $parameterBlacklist = [],
        array $additionalParameters = []
    ) {
        $this->givenAUrlUtil();
        $this->givenARequestFrom($sourceUrl);
        $this->whenHttpsTransformationIsCalled($protocol, $parameterBlacklist, $additionalParameters);
        $this->thenTheTransformedUrlShouldBe($expectedUrl);
    }

    private function givenAUrlUtil()
    {
        $urlPrefixGenerator = $this->prophesize(UrlPrefixGeneratorInterface::class);
        $portalDomainService = $this->prophesize(PortalDomainServiceInterface::class);
        $languageService = $this->prophesize(LanguageServiceInterface::class);
        $domainValidator = $this->prophesize(DomainValidatorInterface::class);
        $domainValidator
            ->getValidDomain(Argument::any(), Argument::any(), Argument::any(), Argument::any())
            ->willReturn('valid-domain.com');
        $authenticityTokenManager = $this->prophesize(AuthenticityTokenManagerInterface::class);
        $authenticityTokenManager->isProtectionEnabled()->willReturn(true);
        $authenticityTokenManager->getTokenPlaceholderAsParameter()->willReturn('cmsauthenticitytoken=[{cmsauthenticitytoken}]');

        $this->urlUtil = new UrlUtil(
            $urlPrefixGenerator->reveal(),
            $portalDomainService->reveal(),
            $languageService->reveal(),
            $domainValidator->reveal(),
            $authenticityTokenManager->reveal()
        );
    }

    /**
     * @return array
     */
    public function getDataForTestTransformHttpUrlToHttpsUrl()
    {
        return [
            ['http://foo.de/', 'https://valid-domain.com/', 'https'],
            ['http://foo.de/?foo=bar', 'https://valid-domain.com/?foo=bar', 'https'],
            ['https://foo.de/', 'https://valid-domain.com/', 'https'],
            ['http://foo.de/bar/baz', 'https://valid-domain.com/bar/baz', 'https'],
            ['http://foo.de/bar/baz?wow=muchAttribute', 'https://valid-domain.com/bar/baz?wow=muchAttribute', 'https'],
            ['http://foo.de/bar/baz?wow=muchAttribute&pagedef=bar', 'https://valid-domain.com/bar/baz?wow=muchAttribute&pagedef=bar', 'https'],

            ['https://foo.de/bar/baz?wow=muchAttribute', 'http://valid-domain.com/bar/baz?wow=muchAttribute', 'http'],

            ['http://foo.de/bar/baz?wow=muchAttribute&foo=bar', 'http://valid-domain.com/bar/baz?wow=muchAttribute', null, ['foo']],

            ['http://foo.de/bar/baz?wow=muchAttribute', 'http://valid-domain.com/bar/baz?wow=muchAttribute&such=value', null, [], ['such' => 'value']],

            ['https://foo.de/bar/baz?wow=muchAttribute', 'http://valid-domain.com/bar/baz?such=value', 'http', ['wow'], ['such' => 'value']],

            ['http://foo.de:8080/', 'https://valid-domain.com/', 'https'],
            ['http://foo.de:8080/', 'http://valid-domain.com:8080/', 'http'],
        ];
    }

    /**
     * creates a request object and simulates the current logic of chameleon where query parameters are not part of the url due to the way, our .htaccess works at the moment.
     *
     * @param string $sourceUrl
     */
    private function givenARequestFrom($sourceUrl)
    {
        $urlParts = explode('?', $sourceUrl);
        $this->request = Request::create($urlParts[0]);
        // todo #28446 - once the htaccess is adjusted, we can create the request with the $sourceUrl
        if (2 === count($urlParts)) {
            $queryParts = explode('&', $urlParts[1]);
            foreach ($queryParts as $queryPart) {
                $keyValue = explode('=', $queryPart);
                $this->request->query->set($keyValue[0], $keyValue[1]);
            }
        }
    }

    /**
     * @param string $protocol
     */
    private function whenHttpsTransformationIsCalled($protocol, array $parameterBlacklist, array $additionalParameters)
    {
        $this->transformedUrl = $this->urlUtil->getModifiedUrlFromRequest($this->request, $protocol, $parameterBlacklist, $additionalParameters);
    }

    /**
     * @param string $expectedUrl
     */
    private function thenTheTransformedUrlShouldBe($expectedUrl)
    {
        $this->assertEquals($expectedUrl, $this->transformedUrl);
    }

    /**
     * @dataProvider getDataForTestRemoveAuthenticityTokenFromUrl
     *
     * @param string $url
     * @param string $expected
     */
    public function testRemoveAuthenticityTokenFromUrl($url, $expected)
    {
        $this->givenAUrlUtil();
        $this->whenRemoveAuthenticityTokenFromUrlIsCalled($url);
        $this->thenTheTransformedUrlShouldBe($expected);
    }

    /**
     * @param string $url
     */
    private function whenRemoveAuthenticityTokenFromUrlIsCalled($url)
    {
        $this->transformedUrl = $this->urlUtil->removeAuthenticityTokenFromUrl($url);
    }

    /**
     * @return array
     */
    public function getDataForTestRemoveAuthenticityTokenFromUrl()
    {
        return [
            [
                'https://foo.com/bar',
                'https://foo.com/bar',
            ],
            [
                'https://foo.com/bar?baz=qux',
                'https://foo.com/bar?baz=qux',
            ],
            [
                'https://foo.com/bar?cmsauthenticitytoken=[{cmsauthenticitytoken}]',
                'https://foo.com/bar',
            ],
            [
                'https://foo.com/bar?cmsauthenticitytoken=[{cmsauthenticitytoken}]&baz=qux',
                'https://foo.com/bar?baz=qux',
            ],
            [
                'https://foo.com/bar?baz=qux&cmsauthenticitytoken=[{cmsauthenticitytoken}]',
                'https://foo.com/bar?baz=qux',
            ],
            [
                'https://foo.com/bar?baz=qux&cmsauthenticitytoken=[{cmsauthenticitytoken}]&quux=corge',
                'https://foo.com/bar?baz=qux&quux=corge',
            ],
        ];
    }

    /**
     * @dataProvider getDataForTestGetAbsoluteUrl
     *
     * @param string $sourceUrl
     * @param bool $secure
     * @param string|null $domain
     * @param string $expectedUrl
     */
    public function testGetAbsoluteUrl($sourceUrl, $secure, $domain, $expectedUrl)
    {
        $this->givenAUrlUtil();
        $this->whenGetAbsoluteUrlIsCalled($sourceUrl, $secure, $domain);
        $this->thenTheTransformedUrlShouldBe($expectedUrl);
    }

    private function whenGetAbsoluteUrlIsCalled($sourceUrl, $secure, $domain)
    {
        $portalMock = $this->getMockBuilder('TdbCmsPortal')
            ->disableAutoload()
            ->getMock();
        $languageMock = $this->getMockBuilder('TdbCmsLanguage')
            ->disableAutoload()
            ->getMock();

        $this->transformedUrl = $this->urlUtil->getAbsoluteUrl($sourceUrl, $secure, $domain, $portalMock, $languageMock);
    }

    /**
     * @return array
     */
    public function getDataForTestGetAbsoluteUrl()
    {
        return [
            [
                '/foo',
                false,
                null,
                'http://valid-domain.com/foo',
            ],
            [
                'http://valid-domain.com/foo',
                false,
                null,
                'http://valid-domain.com/foo',
            ],
            [
                'http://invalid-domain.com/foo',
                false,
                null,
                'http://valid-domain.com/foo',
            ],
            [
                '/foo',
                false,
                'valid-domain.com',
                'http://valid-domain.com/foo',
            ],
            [
                '/foo',
                false,
                'invalid-domain.com',
                'http://valid-domain.com/foo',
            ],
            [
                '/foo',
                true,
                'valid-domain.com',
                'https://valid-domain.com/foo',
            ],
            [
                '/foo',
                true,
                'invalid-domain.com',
                'https://valid-domain.com/foo',
            ],
            [
                'http://valid-domain.com/foo',
                true,
                null,
                'https://valid-domain.com/foo',
            ],
            [
                'https://valid-domain.com/foo',
                true,
                null,
                'https://valid-domain.com/foo',
            ],
            [
                'https://valid-domain.com/foo',
                false,
                null,
                'http://valid-domain.com/foo',
            ],
            [
                'https://invalid-domain.com/foo',
                false,
                null,
                'http://valid-domain.com/foo',
            ],
            [
                'https://invalid-domain.com/foo',
                true,
                null,
                'https://valid-domain.com/foo',
            ],
            [
                '/foo?bar=baz',
                false,
                null,
                'http://valid-domain.com/foo?bar=baz',
            ],
        ];
    }
}
