<?php

namespace ChameleonSystem\CoreBundle\Tests\Routing;

use ChameleonSystem\CoreBundle\Routing\ChameleonFrontendRouter;
use ChameleonSystem\CoreBundle\Routing\DomainValidatorInterface;
use ChameleonSystem\CoreBundle\Service\LanguageServiceInterface;
use ChameleonSystem\CoreBundle\Service\PortalDomainServiceInterface;
use ChameleonSystem\CoreBundle\Util\RoutingUtilInterface;
use ChameleonSystem\CoreBundle\Util\UrlUtil;
use PHPUnit\Framework\MockObject\Stub\ReturnStub;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ChameleonFrontendRouterTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var ChameleonFrontendRouterTestHelper
     */
    private $router;
    /**
     * @var string
     */
    private $actualResult;
    /**
     * @var ContainerInterface|ObjectProphecy
     */
    private $containerMock;
    /**
     * @var UrlGeneratorInterface|ObjectProphecy
     */
    private $generatorMock;
    /**
     * @var \TdbCmsLanguage|\PHPUnit_Framework_MockObject_MockObject
     */
    private $languageMock;
    /**
     * @var LanguageServiceInterface|ObjectProphecy
     */
    private $languageServiceMock;
    /**
     * @var \TdbCmsPortalDomains|\PHPUnit_Framework_MockObject_MockObject
     */
    private $portalDomainMock;
    /**
     * @var PortalDomainServiceInterface
     */
    private $portalDomainServiceMock;
    /**
     * @var \TdbCmsPortal|\PHPUnit_Framework_MockObject_MockObject
     */
    private $portalMock;
    /**
     * @var RequestStack
     */
    private $requestStack;
    /**
     * @var RoutingUtilInterface|ObjectProphecy
     */
    private $routingUtilMock;
    /**
     * @var UrlUtil
     */
    private $urlUtilMock;
    /**
     * @var DomainValidatorInterface|ObjectProphecy
     */
    private $domainValidatorMock;

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        $this->router = null;
        $this->actualResult = null;
        $this->containerMock = null;
        $this->generatorMock = null;
        $this->languageMock = null;
        $this->languageServiceMock = null;
        $this->portalDomainMock = null;
        $this->portalDomainServiceMock = null;
        $this->portalMock = null;
        $this->requestStack = null;
        $this->routingUtilMock = null;
        $this->urlUtilMock = null;
        $this->domainValidatorMock = null;
    }

    /**
     * @dataProvider getDataForGenerateWithPrefixes
     *
     * @param string $routeName
     * @param string|null $portalId
     * @param string $languageCode
     * @param string $activeDomain
     * @param string|null $customDomain
     * @param bool $isCurrentRequestSecure
     * @param string $referenceType
     * @param string $expectedResult
     */
    public function testGenerateWithPrefixes($routeName, $portalId, $languageCode, $activeDomain, $customDomain, $isCurrentRequestSecure, $referenceType, $expectedResult)
    {
        $this->givenAChameleonFrontendRouter($activeDomain, $isCurrentRequestSecure);
        $this->whenGenerateWithPrefixesIsCalled($routeName, $portalId, $languageCode, $customDomain, $isCurrentRequestSecure, $referenceType);
        $this->thenTheExpectedUrlShouldBeGenerated($expectedResult);
    }

    /**
     * @param string $activeDomain
     * @param bool $isCurrentRequestSecure
     */
    private function givenAChameleonFrontendRouter($activeDomain, $isCurrentRequestSecure)
    {
        $this->mockContainer();
        $this->router = new ChameleonFrontendRouterTestHelper($this->containerMock->reveal(), 'dummy-resource');

        $this->mockGenerator($isCurrentRequestSecure);
        $this->router->setGenerator($this->generatorMock->reveal());

        $this->mockRoutingUtil();
        $this->router->setRoutingUtil($this->routingUtilMock->reveal());

        $this->mockRequestStack($isCurrentRequestSecure, $activeDomain);
        $this->router->setRequestStack($this->requestStack);

        $this->mockUrlUtil();
        $this->router->setUrlUtil($this->urlUtilMock->reveal());

        $this->mockPortalDomainService($activeDomain);
        $this->router->setPortalDomainService($this->portalDomainServiceMock->reveal());

        $this->mockLanguageService();
        $this->router->setLanguageService($this->languageServiceMock->reveal());

        $this->mockDomainValidator();
        $this->router->setDomainValidator($this->domainValidatorMock->reveal());
    }

    private function mockContainer()
    {
        $this->containerMock = $this->prophesize('\Symfony\Component\DependencyInjection\ContainerInterface');
        $this->containerMock->getParameter('kernel.environment')->willReturn('test');
        $this->containerMock->getParameter('kernel.cache_dir')->willReturn(sys_get_temp_dir());
    }

    /**
     * @param bool $isCurrentRequestSecure
     */
    private function mockGenerator($isCurrentRequestSecure)
    {
        $this->generatorMock = $this->prophesize('\Symfony\Component\Routing\Generator\UrlGeneratorInterface');
        $this->generatorMock->generate(Argument::any(), Argument::any(), Argument::any())->will(function ($args) use ($isCurrentRequestSecure) {
            $routeName = $args[0];
            $parameters = $args[1];
            $referenceType = $args[2];
            if (false === isset($parameters['domain'])) {
                throw new MissingMandatoryParametersException(sprintf('Some mandatory parameters are missing ("domain") to generate a URL for route "%s".', $routeName));
            }
            /*
             * Using an "unusual" switch-case because of mixture of boolean and string values in the
             * UrlGeneratorInterface constants and PHP comparing the cases with close-enough "==" operator.
             * Thus, the 'relative' case would also match if 'true' was given as $referenceType.
             */
            switch (true) {
                case UrlGeneratorInterface::RELATIVE_PATH === $referenceType:
                    return '/'.$routeName;
                case UrlGeneratorInterface::ABSOLUTE_PATH === $referenceType:
                    return '/'.$routeName;
                case UrlGeneratorInterface::ABSOLUTE_URL === $referenceType:
                    $scheme = $isCurrentRequestSecure ? 'https' : 'http';

                    return $scheme.'://'.$parameters['domain'].'/'.$routeName;
                case UrlGeneratorInterface::NETWORK_PATH === $referenceType:
                    return '//'.$parameters['domain'].'/'.$routeName;
                default:
                    throw new \LogicException('Unknown reference type: '.$referenceType);
            }
        });
    }

    private function mockRoutingUtil()
    {
        $this->routingUtilMock = $this->prophesize('\ChameleonSystem\CoreBundle\Util\RoutingUtilInterface');
        $this->routingUtilMock->getHostRequirementPlaceholder()->willReturn('domain');
    }

    /**
     * @param bool $isCurrentRequestSecure
     * @param string $activeDomain
     */
    private function mockRequestStack($isCurrentRequestSecure, $activeDomain)
    {
        if ($isCurrentRequestSecure) {
            $serverEnv = [
                'HTTPS' => 'on',
            ];
        } else {
            $serverEnv = [
                'HTTPS' => 'off',
            ];
        }
        $serverEnv['HTTP_HOST'] = $activeDomain;
        $this->requestStack = new RequestStack();
        $this->requestStack->push(new Request([], [], [], [], [], $serverEnv));
    }

    private function mockUrlUtil()
    {
        $this->urlUtilMock = $this->prophesize('\ChameleonSystem\CoreBundle\Util\UrlUtil');
    }

    /**
     * @param string $activeDomain
     */
    private function mockPortalDomainService($activeDomain)
    {
        $this->portalDomainMock = $this->getMockBuilder('TdbCmsPortalDomains')
            ->disableAutoload()
            ->setMethods(['GetActiveDomainName'])
            ->getMock();
        $this->portalDomainMock->method('GetActiveDomainName')->willReturn($activeDomain);
        $this->portalDomainServiceMock = $this->prophesize('\ChameleonSystem\CoreBundle\Service\PortalDomainServiceInterface');
        $this->portalDomainServiceMock->getActiveDomain()->willReturn($this->portalDomainMock);
    }

    private function mockLanguageService()
    {
        $this->languageServiceMock = $this->prophesize('\ChameleonSystem\CoreBundle\Service\LanguageServiceInterface');
    }

    private function mockDomainValidator()
    {
        $this->domainValidatorMock = $this->prophesize('\ChameleonSystem\CoreBundle\Routing\DomainValidatorInterface');
        $this->domainValidatorMock->getValidDomain(Argument::any(), Argument::any(), Argument::any(), Argument::any())->willReturn('validated-domain.com');
    }

    /**
     * @param string $routeName
     * @param string|null $portalId
     * @param string $languageCode
     * @param string|null $customDomain
     * @param bool $isCurrentRequestSecure
     * @param string $referenceType
     */
    private function whenGenerateWithPrefixesIsCalled($routeName, $portalId, $languageCode, $customDomain, $isCurrentRequestSecure, $referenceType)
    {
        $this->mockPortal($portalId, $isCurrentRequestSecure);
        if (null === $portalId) {
            $this->portalDomainServiceMock->getActivePortal()->willReturn($this->portalMock);
            $portalArgument = null;
        } else {
            $portalArgument = $this->portalMock;
        }

        $this->mockLanguage($languageCode);
        if (null === $languageCode) {
            $languageArgument = null;
        } else {
            $languageArgument = $this->languageMock;
        }
        $parameters = [];
        if (null !== $customDomain) {
            $parameters['domain'] = $customDomain;
        }

        $this->actualResult = $this->router->generateWithPrefixes($routeName, $parameters, $portalArgument, $languageArgument, $referenceType);
    }

    /**
     * @param string|null $portalId
     * @param bool $isCurrentRequestSecure
     */
    private function mockPortal($portalId, $isCurrentRequestSecure)
    {
        $this->portalMock = $this->getMockBuilder('TdbCmsPortal')
            ->disableAutoload()
            ->setMethods([
                'GetPrimaryDomain',
                ])
            ->getMock();
        $this->portalMock->id = null === $portalId ? '7' : $portalId;
        $this->portalMock->method('GetPrimaryDomain')->will($this->getMockResultForGetPrimaryDomain($isCurrentRequestSecure));
    }

    /**
     * @param string|null $languageCode
     */
    private function mockLanguage($languageCode)
    {
        $this->languageMock = $this
            ->getMockBuilder('TdbCmsLanguage')
            ->disableAutoload()
            ->getMock();
        if (null === $languageCode) {
            $this->languageMock->id = '17';
            $this->languageMock->fieldIso6391 = 'fr';
            $this->languageServiceMock->getActiveLanguage()->willReturn($this->languageMock);
        } else {
            $this->languageMock->id = '1';
            $this->languageMock->fieldIso6391 = $languageCode;
        }
    }

    /**
     * @param bool $isCurrentRequestSecure
     *
     * @return ReturnStub
     */
    private function getMockResultForGetPrimaryDomain($isCurrentRequestSecure)
    {
        if ($isCurrentRequestSecure) {
            return new ReturnStub('secure-primary-domain.com');
        }

        return new ReturnStub('insecure-primary-domain.com');
    }

    /**
     * @param string $expectedResult
     */
    private function thenTheExpectedUrlShouldBeGenerated($expectedResult)
    {
        $this->assertEquals($expectedResult, $this->actualResult);
    }

    /**
     * @return array
     */
    public function getDataForGenerateWithPrefixes()
    {
        return [
            /*
             * Relative URLs
             */
            [
                'routeName' => 'foo',
                'portalId' => '42',
                'languageCode' => 'en',
                'activeDomain' => 'validated-domain.com',
                'customDomain' => null,
                'isCurrentRequestSecure' => false,
                'referenceType' => UrlGeneratorInterface::RELATIVE_PATH,
                'expectedResult' => '/foo-42-en',
            ],
            [
                'routeName' => 'bar',
                'portalId' => '13',
                'languageCode' => 'de',
                'activeDomain' => 'validated-domain.com',
                'customDomain' => null,
                'isCurrentRequestSecure' => false,
                'referenceType' => UrlGeneratorInterface::RELATIVE_PATH,
                'expectedResult' => '/bar-13-de',
            ],
            [
                'routeName' => 'bar',
                'portalId' => null,
                'languageCode' => 'de',
                'activeDomain' => 'validated-domain.com',
                'customDomain' => null,
                'isCurrentRequestSecure' => false,
                'referenceType' => UrlGeneratorInterface::RELATIVE_PATH,
                'expectedResult' => '/bar-7-de',
            ],
            [
                'routeName' => 'bar',
                'portalId' => '42',
                'languageCode' => null,
                'activeDomain' => 'validated-domain.com',
                'customDomain' => null,
                'isCurrentRequestSecure' => false,
                'referenceType' => UrlGeneratorInterface::RELATIVE_PATH,
                'expectedResult' => '/bar-42-fr',
            ],
            [
                'routeName' => 'foo',
                'portalId' => '42',
                'languageCode' => 'en',
                'activeDomain' => 'validated-domain.com',
                'customDomain' => null,
                'isCurrentRequestSecure' => true,
                'referenceType' => UrlGeneratorInterface::RELATIVE_PATH,
                'expectedResult' => '/foo-42-en',
            ],
            /*
             * Relative URL, but the active domain is different from the one returned by the DomainValidator.
             * This case occurs if a URL for an HTTPS-only page is requested in an HTTP request and the HTTPS domain
             * configured in the backend differs from the HTTP domain.
             */
            [
                'routeName' => 'foo',
                'portalId' => '42',
                'languageCode' => 'en',
                'activeDomain' => 'active-domain.com',
                'customDomain' => null,
                'isCurrentRequestSecure' => true,
                'referenceType' => UrlGeneratorInterface::RELATIVE_PATH,
                'expectedResult' => 'https://validated-domain.com/foo-42-en',
            ],
            /*
             * Absolute URLs
             */
            [
                'routeName' => 'foo',
                'portalId' => '42',
                'languageCode' => 'en',
                'activeDomain' => 'active-domain.com',
                'customDomain' => null,
                'isCurrentRequestSecure' => false,
                'referenceType' => UrlGeneratorInterface::ABSOLUTE_URL,
                'expectedResult' => 'http://validated-domain.com/foo-42-en',
            ],
            [
                'routeName' => 'foo',
                'portalId' => '42',
                'languageCode' => 'en',
                'activeDomain' => 'active-domain.com',
                'customDomain' => null,
                'isCurrentRequestSecure' => true,
                'referenceType' => UrlGeneratorInterface::ABSOLUTE_URL,
                'expectedResult' => 'https://validated-domain.com/foo-42-en',
            ],
            /*
             * Network path.
             */
            [
                'routeName' => 'foo',
                'portalId' => '42',
                'languageCode' => 'en',
                'activeDomain' => 'active-domain.com',
                'customDomain' => null,
                'isCurrentRequestSecure' => false,
                'referenceType' => UrlGeneratorInterface::NETWORK_PATH,
                'expectedResult' => '//validated-domain.com/foo-42-en',
            ],
            [
                'routeName' => 'foo',
                'portalId' => '42',
                'languageCode' => 'en',
                'activeDomain' => 'active-domain.com',
                'customDomain' => null,
                'isCurrentRequestSecure' => true,
                'referenceType' => UrlGeneratorInterface::NETWORK_PATH,
                'expectedResult' => '//validated-domain.com/foo-42-en',
            ],
            [
                'routeName' => 'foo',
                'portalId' => '42',
                'languageCode' => 'en',
                'activeDomain' => 'validated-domain.com',
                'customDomain' => null,
                'isCurrentRequestSecure' => true,
                'referenceType' => UrlGeneratorInterface::NETWORK_PATH,
                'expectedResult' => '//validated-domain.com/foo-42-en',
            ],
            /*
             * Custom domain.
             */
            [
                'routeName' => 'foo',
                'portalId' => '42',
                'languageCode' => 'en',
                'activeDomain' => 'active-domain.com',
                'customDomain' => 'custom-domain.com',
                'isCurrentRequestSecure' => false,
                'referenceType' => UrlGeneratorInterface::RELATIVE_PATH,
                'expectedResult' => 'http://validated-domain.com/foo-42-en',
            ],
            /*
             * Custom domain is set, relative path is requested but an absolute URL needs to be returned.
             * Secure and insecure domain names do not differ.
             */
            [
                'routeName' => 'foo',
                'portalId' => '42',
                'languageCode' => 'en',
                'activeDomain' => 'active-domain.com',
                'customDomain' => 'validated-domain.com',
                'isCurrentRequestSecure' => false,
                'referenceType' => UrlGeneratorInterface::RELATIVE_PATH,
                'expectedResult' => 'http://validated-domain.com/foo-42-en',
            ],
            [
                'routeName' => 'foo',
                'portalId' => '42',
                'languageCode' => 'en',
                'activeDomain' => 'active-domain.com',
                'customDomain' => 'validated-domain.com',
                'isCurrentRequestSecure' => true,
                'referenceType' => UrlGeneratorInterface::RELATIVE_PATH,
                'expectedResult' => 'https://validated-domain.com/foo-42-en',
            ],
        ];
    }
}

class ChameleonFrontendRouterTestHelper extends ChameleonFrontendRouter
{
    public function setGenerator(UrlGeneratorInterface $generator)
    {
        $this->generator = $generator;
    }
}
