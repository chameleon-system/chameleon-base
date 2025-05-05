<?php

namespace ChameleonSystem\SecurityBundle\Service;

use ChameleonSystem\SecurityBundle\ChameleonSystemSecurityConstants;
use ChameleonSystem\SecurityBundle\CmsUser\CmsUserDataAccess;
use ChameleonSystem\SecurityBundle\CmsUser\CmsUserModel;
use ChameleonSystem\SecurityBundle\Exception\TwoFactorNotAvailableException;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Scheb\TwoFactorBundle\Security\Authentication\Token\TwoFactorToken;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Google\GoogleAuthenticator;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class TwoFactorService
{
    public function __construct(
        private readonly ?GoogleAuthenticator $googleAuthenticator,
        private readonly TokenStorageInterface $tokenStorage,
        private readonly CmsUserDataAccess $cmsUserDataAccess
    ) {
    }

    public function cloneUserWithTwoFactorSecret(CmsUserModel $user, string $secret = ''): CmsUserModel
    {
        if (null === $this->googleAuthenticator) {
            throw new TwoFactorNotAvailableException('2FA GoogleAuthenticator is not available.');
        }

        if ('' === $secret) {
            $secret = $this->googleAuthenticator->generateSecret();
        }
        $userWithSecret = $user->withGoogleAuthenticatorSecret($secret);

        return $userWithSecret;
    }

    public function saveCmsUserAuthenticatorSecret(CmsUserModel $user): void
    {
        $this->cmsUserDataAccess->setGoogleAuthenticatorSecret($user, $user->getGoogleAuthenticatorSecret());
    }

    /**
     * Generates the the data uri of for the authentiacator qr code
     * based on the user.
     */
    public function generateQrCodeDataUri(CmsUserModel $user): string
    {
        if (null === $this->googleAuthenticator) {
            throw new TwoFactorNotAvailableException('2FA GoogleAuthenticator is not available.');
        }

        $qrContent = $this->googleAuthenticator->getQRContent($user);

        $qrcode = new QrCode($qrContent);
        $writer = new PngWriter();
        $result = $writer->write($qrcode);

        return $result->getDataUri();
    }

    public function checkAuthorizationCode(CmsUserModel $user, string $code): bool
    {
        if (null === $this->googleAuthenticator) {
            throw new \LogicException('2FA GoogleAuthenticator is not available.');
        }

        return $this->googleAuthenticator->checkCode($user, $code);
    }

    public function adjustSessionUser(CmsUserModel $user): void
    {
        $preAuthToken = new UsernamePasswordToken(
            $user,
            ChameleonSystemSecurityConstants::FIREWALL_BACKEND_NAME,
            $user->getRoles()
        );

        $wrappedToken = new TwoFactorToken(
            $preAuthToken,
            '2fa-authenticator', // the active 2FA provider
            ChameleonSystemSecurityConstants::FIREWALL_BACKEND_NAME,
            ['google'], // remaining providers
            0
        );

        $this->tokenStorage->setToken($wrappedToken);
    }
}
