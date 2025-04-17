<?php

namespace ChameleonSystem\SecurityBundle\Tests\Service;

use ChameleonSystem\SecurityBundle\CmsUser\CmsUserDataAccess;
use ChameleonSystem\SecurityBundle\CmsUser\CmsUserModel;
use ChameleonSystem\SecurityBundle\Service\TwoFactorService;
use PHPUnit\Framework\TestCase;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Google\GoogleAuthenticator;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class TwoFactorServiceTest extends TestCase
{
    public function testGenerateQrCodeDataUri(): void
    {
        $user = $this->createMock(CmsUserModel::class);
        $googleAuthenticator = $this->createMock(GoogleAuthenticator::class);
        $googleAuthenticator->method('getQRContent')->willReturn('otpauth://totp/test');

        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $dataAccess = $this->createMock(CmsUserDataAccess::class);

        $service = new TwoFactorService($googleAuthenticator, $tokenStorage, $dataAccess);
        $dataUri = $service->generateQrCodeDataUri($user);

        $this->assertStringStartsWith('data:image/png;base64,', $dataUri);
    }

    public function testCheckAuthorizationCode(): void
    {
        $user = $this->createMock(CmsUserModel::class);
        $googleAuthenticator = $this->createMock(GoogleAuthenticator::class);
        $googleAuthenticator->expects($this->once())
            ->method('checkCode')
            ->with($user, '123456')
            ->willReturn(true);

        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $dataAccess = $this->createMock(CmsUserDataAccess::class);

        $service = new TwoFactorService($googleAuthenticator, $tokenStorage, $dataAccess);
        $this->assertTrue($service->checkAuthorizationCode($user, '123456'));
    }
}
