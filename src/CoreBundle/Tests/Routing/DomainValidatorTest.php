<?php

namespace ChameleonSystem\CoreBundle\Tests\Routing;

use ChameleonSystem\CoreBundle\Routing\DomainValidator;
use ChameleonSystem\CoreBundle\Service\LanguageServiceInterface;
use ChameleonSystem\CoreBundle\Service\PortalDomainServiceInterface;
use ChameleonSystem\CoreBundle\Service\RequestInfoServiceInterface;
use PHPUnit\Framework\MockObject\Stub\ReturnStub;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\HttpFoundation\RequestStack;

class DomainValidatorTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var DomainValidator
     */
    private $domainValidator;
    /**
     * @var string
     */
    private $actualResult;
    /**
     * @var \TdbCmsPortalDomains|\PHPUnit_Framework_MockObject_MockObject
     */
    private $portalDomainMock;
    /**
     * @var PortalDomainServiceInterface|ObjectProphecy
     */
    private $portalDomainServiceMock;
    /**
     * @var \TdbCmsPortal|\PHPUnit_Framework_MockObject_MockObject
     */
    private $portalMock;
    /**
     * @var RequestInfoServiceInterface|ObjectProphecy
     */
    private $requestInfoServiceMock;
    /**
     * @var RequestStack
     */
    private $requestStackMock;
    /**
     * @var LanguageServiceInterface|ObjectProphecy
     */
    private $languageServiceMock;
    /**
     * @var \TdbCmsLanguage|\PHPUnit_Framework_MockObject_MockObject
     */
    private $language1Mock;
    /**
     * @var \TdbCmsLanguage|\PHPUnit_Framework_MockObject_MockObject
     */
    private $language2Mock;

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        $this->domainValidator = null;
        $this->actualResult = null;
        $this->portalDomainMock = null;
        $this->portalDomainServiceMock = null;
        $this->portalMock = null;
        $this->requestInfoServiceMock = null;
        $this->requestStackMock = null;
        $this->languageServiceMock = null;
        $this->language1Mock = null;
        $this->language2Mock = null;
    }

    /**
     * @dataProvider getDataForTestGetValidatedDomain
     *
     * @param string $domain
     * @param string $portalId
     * @param string $languageId
     * @param bool $secure
     * @param bool $forcePrimaryDomain
     * @param bool $isBackendMode
     * @param bool $isCurrentRequestSecure
     * @param string $expectedValue
     */
    public function testGetValidatedDomain($domain, $portalId, $languageId, $secure, $forcePrimaryDomain, $isBackendMode, $isCurrentRequestSecure, $expectedValue)
    {
        $this->givenADomainValidator($languageId, $forcePrimaryDomain, $secure, $isBackendMode, $isCurrentRequestSecure);
        $this->whenGetValidatedDomainIsCalled($domain, $portalId, $languageId, $secure);
        $this->thenTheCorrectDomainIsExpected($expectedValue);
    }

    /**
     * @param string $languageId
     * @param bool $forcePrimaryDomain
     * @param bool $secureArgument
     * @param bool $isBackendMode
     * @param bool $isCurrentRequestSecure
     */
    private function givenADomainValidator($languageId, $forcePrimaryDomain, $secureArgument, $isBackendMode, $isCurrentRequestSecure)
    {
        $this->mockPortalDomainService($languageId, $secureArgument, $isBackendMode);
        $this->mockRequestInfoService($isBackendMode);
        $this->mockRequestStack($isCurrentRequestSecure);
        $this->mockLanguageService();

        $this->domainValidator = new DomainValidator($this->portalDomainServiceMock->reveal(), $this->requestInfoServiceMock->reveal(), $this->requestStackMock, $this->languageServiceMock->reveal(), $forcePrimaryDomain);
    }

    /**
     * @param string $languageId
     * @param bool $secure
     * @param bool $isBackendMode
     */
    private function mockPortalDomainService($languageId, $secure, $isBackendMode)
    {
        $this->portalDomainMock = $this->getMockBuilder('TdbCmsPortalDomains')
            ->disableAutoload()
            ->setMethods(['GetActiveDomainName'])
            ->getMock();
        $this->portalDomainMock->method('GetActiveDomainName')->willReturn('active-domain.com');

        $this->portalMock = $this->getMockBuilder('TdbCmsPortal')
            ->disableAutoload()
            ->setMethods([
                'GetFieldCmsPortalDomainsList',
            ])
            ->getMock();
        $this->portalMock->method('GetFieldCmsPortalDomainsList')->will($this->getMockResultForGetFieldCmsPortalDomainsList());
        $this->portalMock->id = '1';

        $this->portalDomainServiceMock = $this->prophesize('\ChameleonSystem\CoreBundle\Service\PortalDomainServiceInterface');
        if ($isBackendMode) {
            $this->portalDomainServiceMock->getActiveDomain()->willReturn(null);
            $this->portalDomainServiceMock->getActivePortal()->willReturn(null);
        } else {
            $this->portalDomainServiceMock->getActiveDomain()->willReturn($this->portalDomainMock);
            $this->portalDomainServiceMock->getActivePortal()->willReturn($this->portalMock);
        }

        $portalDomainMockPrimaryLanguage1 = $this->getMockBuilder('TdbCmsPortalDomains')
            ->disableAutoload()
            ->setMethods(['getSecureDomainName', 'getInsecureDomainName'])
            ->getMock();
        $portalDomainMockPrimaryLanguage1->method('getSecureDomainName')->willReturn('secure-primary-domain-with-language1.com');
        $portalDomainMockPrimaryLanguage1->method('getInsecureDomainName')->willReturn('insecure-primary-domain-with-language1.com');
        $this->portalDomainServiceMock->getPrimaryDomain(Argument::any(), '1')->willReturn($portalDomainMockPrimaryLanguage1);

        $portalDomainMockPrimaryLanguage2 = $this->getMockBuilder('TdbCmsPortalDomains')
            ->disableAutoload()
            ->setMethods(['getSecureDomainName', 'getInsecureDomainName'])
            ->getMock();
        $portalDomainMockPrimaryLanguage2->method('getSecureDomainName')->willReturn('secure-primary-domain-without-language.com');
        $portalDomainMockPrimaryLanguage2->method('getInsecureDomainName')->willReturn('insecure-primary-domain-without-language.com');
        $this->portalDomainServiceMock->getPrimaryDomain(Argument::any(), Argument::not('1'))->willReturn($portalDomainMockPrimaryLanguage2);
    }

    /**
     * @param bool $isBackendMode
     */
    private function mockRequestInfoService($isBackendMode)
    {
        $this->requestInfoServiceMock = $this->prophesize('\ChameleonSystem\CoreBundle\Service\RequestInfoServiceInterface');
        $this->requestInfoServiceMock->isBackendMode()->willReturn($isBackendMode);
    }

    private function mockRequestStack($isCurrentRequestSecure)
    {
        $request = $this->prophesize('\Symfony\Component\HttpFoundation\Request');
        $request->getHost()->willReturn('active-domain.com');
        $request->isSecure()->willReturn($isCurrentRequestSecure);

        $this->requestStackMock = new RequestStack();
        $this->requestStackMock->push($request->reveal());
    }

    private function mockLanguageService()
    {
        $this->language1Mock = $this->getMockBuilder('TdbCmsLanguage')->disableAutoload()->getMock();
        $this->language1Mock->id = '1';

        $this->language2Mock = $this->getMockBuilder('TdbCmsLanguage')->disableAutoload()->getMock();
        $this->language2Mock->id = '2';

        $this->languageServiceMock = $this->prophesize('\ChameleonSystem\CoreBundle\Service\LanguageServiceInterface');
        $this->languageServiceMock->getActiveLanguage()->willReturn($this->language1Mock);
    }

    /**
     * @param string $domain
     * @param string $portalId
     * @param string $languageId
     * @param bool $secure
     */
    private function whenGetValidatedDomainIsCalled($domain, $portalId, $languageId, $secure)
    {
        $portal = $this->getMockPortalForId($portalId);
        $language = $this->getMockLanguageForId($languageId);

        $this->actualResult = $this->domainValidator->getValidDomain($domain, $portal, $language, $secure);
    }

    /**
     * @param string $portalId
     *
     * @return \TdbCmsPortal|\PHPUnit_Framework_MockObject_MockObject|null
     */
    private function getMockPortalForId($portalId)
    {
        if ('1' === $portalId) {
            return $this->portalMock;
        }

        return null;
    }

    /**
     * @return ReturnStub
     */
    private function getMockResultForGetFieldCmsPortalDomainsList()
    {
        $domainListMock = $this
            ->getMockBuilder('TdbCmsPortalDomainList')
            ->disableAutoload()
            ->setMethods([
                'Next',
            ])
            ->getMock();

        $domainMock1 = $this
            ->getMockBuilder('TdbCmsPortalDomains')
            ->disableAutoload()
            ->setMethods([
                'getInsecureDomainName',
                'getSecureDomainName',
            ])
            ->getMock();
        $domainMock1->fieldCmsLanguageId = '1';
        $domainMock1->method('getInsecureDomainName')->willReturn('insecure-primary-domain-with-language1.com');
        $domainMock1->method('getSecureDomainName')->willReturn('secure-primary-domain-with-language1.com');
        $domainMock1->fieldIsMasterDomain = true;

        $domainMock2 = $this
            ->getMockBuilder('TdbCmsPortalDomains')
            ->disableAutoload()
            ->setMethods([
                'getInsecureDomainName',
                'getSecureDomainName',
            ])
            ->getMock();
        $domainMock2->fieldCmsLanguageId = '2';
        $domainMock2->method('getInsecureDomainName')->willReturn('insecure-domain-with-language2.com');
        $domainMock2->method('getSecureDomainName')->willReturn('secure-domain-with-language2.com');
        $domainMock2->fieldIsMasterDomain = false;

        $domainMock3 = $this
            ->getMockBuilder('TdbCmsPortalDomains')
            ->disableAutoload()
            ->setMethods([
                'getInsecureDomainName',
                'getSecureDomainName',
            ])
            ->getMock();
        $domainMock3->fieldCmsLanguageId = '';
        $domainMock3->method('getInsecureDomainName')->willReturn('insecure-primary-domain-without-language.com');
        $domainMock3->method('getSecureDomainName')->willReturn('secure-primary-domain-without-language.com');
        $domainMock3->fieldIsMasterDomain = true;

        $domainMock4 = $this
            ->getMockBuilder('TdbCmsPortalDomains')
            ->disableAutoload()
            ->setMethods([
                'getInsecureDomainName',
                'getSecureDomainName',
            ])
            ->getMock();
        $domainMock4->fieldCmsLanguageId = '';
        $domainMock4->method('getInsecureDomainName')->willReturn('domain-without-separate-secure-primary-domain-without-language.com');
        $domainMock4->method('getSecureDomainName')->willReturn('domain-without-separate-secure-primary-domain-without-language.com');
        $domainMock4->fieldIsMasterDomain = false;

        $domainMock5 = $this
            ->getMockBuilder('TdbCmsPortalDomains')
            ->disableAutoload()
            ->setMethods([
                'getInsecureDomainName',
                'getSecureDomainName',
            ])
            ->getMock();
        $domainMock5->fieldCmsLanguageId = '';
        $domainMock5->method('getInsecureDomainName')->willReturn('active-domain.com');
        $domainMock5->method('getSecureDomainName')->willReturn('active-domain.com');
        $domainMock5->fieldIsMasterDomain = false;

        $domainListMock->method('Next')->willReturn($domainMock1, $domainMock2, $domainMock3, $domainMock4, $domainMock5);

        return new ReturnStub($domainListMock);
    }

    /**
     * @param string $languageId
     *
     * @return \TdbCmsLanguage|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getMockLanguageForId($languageId)
    {
        if ('1' === $languageId) {
            return $this->language1Mock;
        }

        return $this->language2Mock;
    }

    /**
     * @param string $expectedValue
     */
    private function thenTheCorrectDomainIsExpected($expectedValue)
    {
        $this->assertEquals($expectedValue, $this->actualResult);
    }

    /**
     * @return array
     */
    public function getDataForTestGetValidatedDomain()
    {
        return [
            [
                'domain' => null,
                'portalId' => null,
                'languageId' => '1',
                'secure' => false,
                'forcePrimaryDomain' => false,
                'isBackendMode' => false,
                'isCurrentRequestSecure' => false,
                'expectedResult' => 'active-domain.com',
            ],
            [
                'domain' => null,
                'portalId' => null,
                'languageId' => '1',
                'secure' => false,
                'forcePrimaryDomain' => true,
                'isBackendMode' => false,
                'isCurrentRequestSecure' => false,
                'expectedResult' => 'insecure-primary-domain-with-language1.com',
            ],
            [
                'domain' => 'insecure-primary-domain-without-language.com',
                'portalId' => '1',
                'languageId' => '1',
                'secure' => false,
                'forcePrimaryDomain' => false,
                'isBackendMode' => false,
                'isCurrentRequestSecure' => false,
                'expectedResult' => 'insecure-primary-domain-without-language.com',
            ],
            [
                'domain' => 'secure-primary-domain-without-language.com',
                'portalId' => '1',
                'languageId' => '1',
                'secure' => true,
                'forcePrimaryDomain' => false,
                'isBackendMode' => false,
                'isCurrentRequestSecure' => false,
                'expectedResult' => 'secure-primary-domain-without-language.com',
            ],
            [
                'domain' => 'secure-primary-domain-with-language1.com',
                'portalId' => '1',
                'languageId' => '1',
                'secure' => true,
                'forcePrimaryDomain' => false,
                'isBackendMode' => false,
                'isCurrentRequestSecure' => false,
                'expectedResult' => 'secure-primary-domain-with-language1.com',
            ],
            [
                'domain' => 'secure-primary-domain-with-language1.com',
                'portalId' => '1',
                'languageId' => '1',
                'secure' => true,
                'forcePrimaryDomain' => false,
                'isBackendMode' => false,
                'isCurrentRequestSecure' => true,
                'expectedResult' => 'secure-primary-domain-with-language1.com',
            ],
            [
                'domain' => 'secure-domain-with-language2.com',
                'portalId' => '1',
                'languageId' => '2',
                'secure' => true,
                'forcePrimaryDomain' => false,
                'isBackendMode' => false,
                'isCurrentRequestSecure' => false,
                'expectedResult' => 'secure-domain-with-language2.com',
            ],
            [
                'domain' => 'invalid-domain.com',
                'portalId' => '1',
                'languageId' => '1',
                'secure' => true,
                'forcePrimaryDomain' => false,
                'isBackendMode' => false,
                'isCurrentRequestSecure' => true,
                'expectedResult' => 'active-domain.com',
            ],
            [
                'domain' => 'invalid-domain.com',
                'portalId' => '1',
                'languageId' => '1',
                'secure' => true,
                'forcePrimaryDomain' => true,
                'isBackendMode' => false,
                'isCurrentRequestSecure' => false,
                'expectedResult' => 'secure-primary-domain-with-language1.com',
            ],
            [
                'domain' => 'active-domain.com',
                'portalId' => '1',
                'languageId' => '1',
                'secure' => true,
                'forcePrimaryDomain' => false,
                'isBackendMode' => false,
                'isCurrentRequestSecure' => true,
                'expectedResult' => 'active-domain.com',
            ],
            [
                'domain' => 'active-domain.com',
                'portalId' => '1',
                'languageId' => '1',
                'secure' => true,
                'forcePrimaryDomain' => false,
                'isBackendMode' => false,
                'isCurrentRequestSecure' => false,
                'expectedResult' => 'active-domain.com',
            ],
            [
                'domain' => 'active-domain.com',
                'portalId' => '1',
                'languageId' => '1',
                'secure' => true,
                'forcePrimaryDomain' => true,
                'isBackendMode' => false,
                'isCurrentRequestSecure' => true,
                'expectedResult' => 'secure-primary-domain-with-language1.com',
            ],
            /*
             * Request primary domain for language 1, but with language 2 which has no explicit primary domain.
             * Therefore primary domain without language is expected.
             */
            [
                'domain' => 'secure-primary-domain-with-language1.com',
                'portalId' => '1',
                'languageId' => '2',
                'secure' => true,
                'forcePrimaryDomain' => true,
                'isBackendMode' => false,
                'isCurrentRequestSecure' => true,
                'expectedResult' => 'secure-primary-domain-without-language.com',
            ],
            /*
             * Backend.
             */
            [
                'domain' => 'insecure-primary-domain-without-language.com',
                'portalId' => '1',
                'languageId' => '1',
                'secure' => false,
                'forcePrimaryDomain' => false,
                'isBackendMode' => true,
                'isCurrentRequestSecure' => false,
                'expectedResult' => 'insecure-primary-domain-without-language.com',
            ],
            [
                'domain' => 'invalid-domain.com',
                'portalId' => '1',
                'languageId' => '1',
                'secure' => true,
                'forcePrimaryDomain' => false,
                'isBackendMode' => true,
                'isCurrentRequestSecure' => false,
                'expectedResult' => 'secure-primary-domain-with-language1.com',
            ],
            /*
             * If a domain is requested for an invalid domain (i.e. one that is not registered as portal domain) while
             * in the backend, and if the domain is not requested for a specific portal, the domain is regarded as
             * valid. This is because the backend can be run on any domain, not just on frontend domains.
             */
            [
                'domain' => 'invalid-domain.com',
                'portalId' => null,
                'languageId' => '1',
                'secure' => true,
                'forcePrimaryDomain' => false,
                'isBackendMode' => true,
                'isCurrentRequestSecure' => false,
                'expectedResult' => 'invalid-domain.com',
            ],
            [
                'domain' => 'invalid-domain.com',
                'portalId' => null,
                'languageId' => '1',
                'secure' => true,
                'forcePrimaryDomain' => false,
                'isBackendMode' => true,
                'isCurrentRequestSecure' => true,
                'expectedResult' => 'invalid-domain.com',
            ],
            [
                'domain' => 'invalid-domain.com',
                'portalId' => null,
                'languageId' => '1',
                'secure' => false,
                'forcePrimaryDomain' => true,
                'isBackendMode' => true,
                'isCurrentRequestSecure' => true,
                'expectedResult' => 'invalid-domain.com',
            ],
            /*
             * If no specific domain is requested, the domain of the current request is returned.
             */
            [
                'domain' => null,
                'portalId' => null,
                'languageId' => '1',
                'secure' => true,
                'forcePrimaryDomain' => false,
                'isBackendMode' => true,
                'isCurrentRequestSecure' => false,
                'expectedResult' => 'active-domain.com',
            ],
        ];
    }
}
