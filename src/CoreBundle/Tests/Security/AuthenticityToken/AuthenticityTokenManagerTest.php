<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Tests\Security\AuthenticityToken;

use ChameleonSystem\CoreBundle\Security\AuthenticityToken\AuthenticityTokenManager;
use ChameleonSystem\CoreBundle\Security\AuthenticityToken\AuthenticityTokenManagerInterface;
use ChameleonSystem\CoreBundle\Security\AuthenticityToken\InvalidTokenFormatException;
use ChameleonSystem\CoreBundle\Service\RequestInfoServiceInterface;
use ChameleonSystem\CoreBundle\Tests\Security\AuthenticityToken\fixtures\AuthenticityTokenStorageMock;
use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;

class AuthenticityTokenManagerTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var bool
     */
    private $enabledInBackend;
    /**
     * @var bool
     */
    private $enabledInFrontend;
    /**
     * @var RequestInfoServiceInterface|ObjectProphecy
     */
    private $requestInfoServiceMock;
    /**
     * @var CsrfTokenManagerInterface
     */
    private $csrfTokenManager;
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;
    /**
     * @var InputFilterUtilInterface|ObjectProphecy
     */
    private $inputFilterUtilMock;
    /**
     * @var AuthenticityTokenManager
     */
    private $subject;
    /**
     * @var bool|string|array
     */
    private $actualResult;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        if (false === defined('TCMSUSERINPUT_DEFAULTFILTER')) {
            define('TCMSUSERINPUT_DEFAULTFILTER', 'TCMSUserInput_BaseText');
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        $this->enabledInBackend = null;
        $this->enabledInFrontend = null;
        $this->requestInfoServiceMock = null;
        $this->csrfTokenManager = null;
        $this->tokenStorage = null;
        $this->inputFilterUtilMock = null;
        $this->subject = null;
        $this->actualResult = null;
    }

    /**
     * @dataProvider provideDataForTestIsProtectionEnabled
     *
     * @param bool $authenticityTokenUsageEnabledInBackend
     * @param bool $authenticityTokenUsageEnabledInFrontend
     * @param bool $isBackendMode
     * @param bool $isCmsTemplateEngineEditMode
     * @param bool $expectedResult
     */
    public function testIsProtectionEnabled($authenticityTokenUsageEnabledInBackend, $authenticityTokenUsageEnabledInFrontend, $isBackendMode, $isCmsTemplateEngineEditMode, $expectedResult): void
    {
        $this->givenAChameleonMode($isBackendMode, $isCmsTemplateEngineEditMode);
        $this->givenAuthenticityTokenHandlingIsEnabledInBackend($authenticityTokenUsageEnabledInBackend);
        $this->givenAuthenticityTokenHandlingIsEnabledInFrontend($authenticityTokenUsageEnabledInFrontend);
        $this->givenAnAuthenticityTokenManager();

        $this->whenIsProtectionEnabledIsCalled();

        $this->thenTheExactExpectedResultShouldBeReturned($expectedResult);
    }

    public function provideDataForTestIsProtectionEnabled()
    {
        return [
            'disabled' => [
                false,
                false,
                false,
                false,
                false,
            ],
            'frontend' => [
                false,
                true,
                false,
                false,
                true,
            ],
            'backend' => [
                true,
                false,
                true,
                false,
                true,
            ],
            'templateengine' => [
                true,
                false,
                false,
                true,
                true,
            ],
            'all-on' => [
                true,
                true,
                true,
                true,
                true,
            ],
        ];
    }

    private function givenAChameleonMode(bool $isBackendMode, bool $isCmsTemplateEngineEditMode): void
    {
        $this->requestInfoServiceMock = $this->prophesize(RequestInfoServiceInterface::class);
        $this->requestInfoServiceMock->isBackendMode()->willReturn($isBackendMode);
        $this->requestInfoServiceMock->isCmsTemplateEngineEditMode()->willReturn($isCmsTemplateEngineEditMode);
    }

    private function givenAuthenticityTokenHandlingIsEnabledInBackend(bool $enabled): void
    {
        $this->enabledInBackend = $enabled;
    }

    private function givenAuthenticityTokenHandlingIsEnabledInFrontend(bool $enabled): void
    {
        $this->enabledInFrontend = $enabled;
    }

    private function givenAnAuthenticityTokenManager(): void
    {
        if (null === $this->tokenStorage) {
            $this->tokenStorage = new AuthenticityTokenStorageMock();
        }
        $this->csrfTokenManager = new CsrfTokenManager(null, $this->tokenStorage);
        if (null === $this->requestInfoServiceMock) {
            $this->givenAChameleonMode(false, false);
        }
        if (null === $this->inputFilterUtilMock) {
            $this->inputFilterUtilMock = $this->prophesize(InputFilterUtilInterface::class);
        }

        $this->subject = new AuthenticityTokenManager(
            $this->csrfTokenManager,
            $this->requestInfoServiceMock->reveal(),
            $this->inputFilterUtilMock->reveal(),
            $this->enabledInBackend,
            $this->enabledInFrontend
        );
    }

    private function whenIsProtectionEnabledIsCalled(): void
    {
        $this->actualResult = $this->subject->isProtectionEnabled();
    }

    /**
     * @param bool|string|array $expectedResult
     */
    private function thenTheExpectedResultShouldBeReturned($expectedResult): void
    {
        if (is_string($expectedResult)) {
            $this->assertIsString($this->actualResult);
            $this->assertStringStartsWith($expectedResult, $this->actualResult);

            return;
        }
        if (is_array($expectedResult)) {
            $this->assertIsArray($this->actualResult);
            foreach ($expectedResult as $expectedKey) {
                $this->assertArrayHasKey($expectedKey, $this->actualResult);
            }

            return;
        }
        $this->assertTrue(false, 'Expected either a string or an array');
    }

    /**
     * @param bool|string|array $expectedResult
     */
    private function thenTheExactExpectedResultShouldBeReturned($expectedResult): void
    {
        $this->assertEquals($expectedResult, $this->actualResult);
    }

    /**
     * @dataProvider provideDataForTestIsAuthenticityTokenValid
     */
    public function testIsTokenValid(bool $authenticityTokenUsageEnabled, ?string $submittedToken, ?string $storedToken, bool $expectedResult): void
    {
        $this->givenWeAreInFrontendMode();
        $this->givenAuthenticityTokenHandlingIsEnabledInFrontend($authenticityTokenUsageEnabled);
        $this->givenTokenIsStored($storedToken);
        $this->givenTheUserSubmittedToken($submittedToken);
        $this->givenAnAuthenticityTokenManager();

        $this->whenIsTokenValidIsCalled();

        $this->thenTheExactExpectedResultShouldBeReturned($expectedResult);

        if (null === $submittedToken) {
            $this->thenFilteredGetInputShouldHaveBeenCalled();
        } else {
            $this->thenFilteredGetInputShouldNotHaveBeenCalled();
        }
    }

    public function provideDataForTestIsAuthenticityTokenValid(): array
    {
        return [
            'disabled' => [
                false,
                'foo',
                'bar',
                true,
            ],
            'validToken' => [
                true,
                'token',
                'token',
                true,
            ],
            'invalidToken' => [
                true,
                'token1',
                'token2',
                false,
            ],
            'noSubmittedToken' => [
                true,
                null,
                'token1',
                false,
            ],
            'emptySubmittedToken' => [
                true,
                '',
                'token1',
                false,
            ],
            'noStoredToken' => [
                true,
                'token1',
                null,
                false,
            ],
            'emptyStoredToken' => [
                true,
                'token1',
                '',
                false,
            ],
        ];
    }

    private function givenWeAreInFrontendMode(): void
    {
        $this->givenAChameleonMode(false, false);
    }

    private function givenTokenIsStored(?string $tokenValue): void
    {
        if (null === $this->tokenStorage) {
            $this->tokenStorage = new AuthenticityTokenStorageMock();
        }
        if (null !== $tokenValue) {
            $this->tokenStorage->setToken('cmsauthenticitytoken', $tokenValue);
        }
    }

    private function givenTheUserSubmittedToken(?string $tokenValue): void
    {
        if (null === $this->inputFilterUtilMock) {
            $this->inputFilterUtilMock = $this->prophesize(InputFilterUtilInterface::class);
        }
        $this->inputFilterUtilMock->getFilteredPostInput('cmsauthenticitytoken')->willReturn($tokenValue);
        $this->inputFilterUtilMock->getFilteredGetInput('cmsauthenticitytoken')->willReturn(null);
    }

    private function whenIsTokenValidIsCalled(): void
    {
        $this->actualResult = $this->subject->isTokenValid();
    }

    private function thenFilteredGetInputShouldHaveBeenCalled(): void
    {
        $this->inputFilterUtilMock->getFilteredGetInput('cmsauthenticitytoken')->shouldHaveBeenCalled();
    }

    private function thenFilteredGetInputShouldNotHaveBeenCalled(): void
    {
        $this->inputFilterUtilMock->getFilteredGetInput('cmsauthenticitytoken')->shouldNotHaveBeenCalled();
    }

    public function testRefreshToken(): void
    {
        $this->givenWeAreInFrontendMode();
        $this->givenTokenIsStored('my-token');
        $this->givenAnAuthenticityTokenManager();

        $this->whenRefreshTokenIsCalled();

        $this->thenANewRandomTokenShouldBeStored();
    }

    private function whenRefreshTokenIsCalled(): void
    {
        $this->subject->refreshToken();
    }

    private function thenANewRandomTokenShouldBeStored(): void
    {
        $token = $this->tokenStorage->getToken('cmsauthenticitytoken');
        $this->assertNotNull($token);
        $this->assertNotEquals('', $token);
        $this->assertNotEquals('my-token', $token);
    }

    public function testGetStoredTokenWithStoredToken(): void
    {
        $this->givenWeAreInFrontendMode();
        $this->givenTokenIsStored('very-token');
        $this->givenAnAuthenticityTokenManager();

        $this->whenGetStoredTokenIsCalled();

        $this->thenTheStoredAuthenticityTokenShouldBeReturned('cmsauthenticitytoken');
    }

    public function thenTheStoredAuthenticityTokenShouldBeReturned(string $tokenId): void
    {
        $this->assertEquals('very-token', $this->tokenStorage->getToken($tokenId));
    }

    private function whenGetStoredTokenIsCalled(): void
    {
        $this->actualResult = $this->subject->getStoredToken();
    }

    public function testGetStoredTokenWithoutStoredToken(): void
    {
        $this->givenWeAreInFrontendMode();
        $this->givenAnAuthenticityTokenManager();

        $this->whenGetStoredTokenIsCalled();

        $this->thenANewRandomTokenShouldBeStored();
    }

    /**
     * @dataProvider provideDataForTestAddTokenToForms
     */
    public function testAddTokenToForms(string $inputPath, string $expectationPath): void
    {
        $this->givenWeAreInFrontendMode();
        $this->givenTokenIsStored('some-token');
        $this->givenAnAuthenticityTokenManager();

        $this->whenAddTokenToFormsIsCalled($inputPath);

        $this->thenTheContentOfThisPathShouldBeReturned($expectationPath);
    }

    public function provideDataForTestAddTokenToForms(): array
    {
        return [
            'singleForm' => [
                __DIR__.'/fixtures/singleFormInput.html',
                __DIR__.'/fixtures/singleFormExpected.html',
            ],
            'multipleForms' => [
                __DIR__.'/fixtures/multipleFormsInput.html',
                __DIR__.'/fixtures/multipleFormsExpected.html',
            ],
            'noModuleFunction' => [
                __DIR__.'/fixtures/noModuleFunctionInput.html',
                __DIR__.'/fixtures/noModuleFunctionExpected.html',
            ],
            'singleFormButton' => [
                __DIR__.'/fixtures/singleFormButtonInput.html',
                __DIR__.'/fixtures/singleFormButtonExpected.html',
            ],
            'noForm' => [
                __DIR__.'/fixtures/noFormInput.html',
                __DIR__.'/fixtures/noFormExpected.html',
            ],
        ];
    }

    private function whenAddTokenToFormsIsCalled(string $inputPath): void
    {
        $this->actualResult = $this->subject->addTokenToForms(\file_get_contents($inputPath));
    }

    private function thenTheContentOfThisPathShouldBeReturned(string $expectationPath): void
    {
        // todo: this will not test the randomization of the token as it is private to the CsrfTokenManager. This should be tested somehow as well
        $result = preg_replace('/<input type="hidden" name="cmsauthenticitytoken" value="[^"]+"/', '<input type="hidden" name="cmsauthenticitytoken" value="some-token"', $this->actualResult);
        $this->assertStringEqualsFile($expectationPath, $result);
    }

    /**
     * @dataProvider provideDataForTestGetTokenPlaceholderAsParameter
     *
     * @param string|array $expectedValue
     */
    public function testGetTokenPlaceholderAsParameter(int $format, $expectedValue): void
    {
        $this->givenWeAreInBackendMode();
        $this->givenTokenIsStored('some-token');
        $this->givenAnAuthenticityTokenManager();

        $this->whenGetTokenPlaceholderAsParameterIsCalled($format);

        $this->thenTheExactExpectedResultShouldBeReturned($expectedValue);
    }

    private function givenWeAreInBackendMode(): void
    {
        $this->givenAChameleonMode(true, false);
    }

    public function provideDataForTestGetTokenPlaceholderAsParameter(): array
    {
        return [
            [
                AuthenticityTokenManagerInterface::TOKEN_FORMAT_GET,
                'cmsauthenticitytoken=[{cmsauthenticitytoken}]',
            ],
            [
                AuthenticityTokenManagerInterface::TOKEN_FORMAT_POST,
                '<input type="hidden" name="cmsauthenticitytoken" value="[{cmsauthenticitytoken}]" />',
            ],
            [
                AuthenticityTokenManagerInterface::TOKEN_FORMAT_ARRAY,
                [
                    'cmsauthenticitytoken' => '[{cmsauthenticitytoken}]',
                ],
            ],
        ];
    }

    private function whenGetTokenPlaceholderAsParameterIsCalled(int $format): void
    {
        $this->actualResult = $this->subject->getTokenPlaceholderAsParameter($format);
    }

    public function testGetTokenPlaceholderAsParameterException(): void
    {
        $this->givenAnAuthenticityTokenManager();

        $this->thenAnInvalidTokenFormatExceptionShouldBeThrown();

        $this->whenGetTokenPlaceholderAsParameterIsCalled(1337);
    }

    private function thenAnInvalidTokenFormatExceptionShouldBeThrown()
    {
        $this->expectException(InvalidTokenFormatException::class);
    }

    /**
     * @dataProvider provideDataForTestGetResolvedTokenAsParameter
     *
     * @param string|array $expectedValue
     */
    public function testGetResolvedTokenAsParameter(int $format, $expectedValue): void
    {
        $this->givenWeAreInFrontendMode();
        $this->givenTokenIsStored('some-token');
        $this->givenAnAuthenticityTokenManager();

        $this->whenGetResolvedTokenAsParameterIsCalled($format);

        $this->thenTheExpectedResultShouldBeReturned($expectedValue);
    }

    public function provideDataForTestGetResolvedTokenAsParameter(): array
    {
        return [
            [
                AuthenticityTokenManagerInterface::TOKEN_FORMAT_GET,
                'cmsauthenticitytoken=',
            ],
            [
                AuthenticityTokenManagerInterface::TOKEN_FORMAT_POST,
                '<input type="hidden" name="cmsauthenticitytoken" value="',
            ],
            [
                AuthenticityTokenManagerInterface::TOKEN_FORMAT_ARRAY,
                [
                    'cmsauthenticitytoken',
                ],
            ],
        ];
    }

    private function whenGetResolvedTokenAsParameterIsCalled(int $format): void
    {
        $this->actualResult = $this->subject->getResolvedTokenAsParameter($format);
    }

    public function testGetResolvedTokenAsParameterException(): void
    {
        $this->givenAnAuthenticityTokenManager();

        $this->thenAnInvalidTokenFormatExceptionShouldBeThrown();

        $this->whenGetResolvedTokenAsParameterIsCalled(1337);
    }
}
