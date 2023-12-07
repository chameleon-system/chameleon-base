<?php

namespace ChameleonSystem\SecurityBundle\CmsGoogleLogin;

use ChameleonSystem\CoreBundle\Interfaces\GuidCreationServiceInterface;
use ChameleonSystem\SecurityBundle\CmsUser\CmsUserDataAccess;
use ChameleonSystem\SecurityBundle\CmsUser\CmsUserModel;
use ChameleonSystem\SecurityBundle\CmsUser\CmsUserSSOModel;
use League\OAuth2\Client\Provider\GoogleUser;

class GoogleUserRegistrationService implements GoogleUserRegistrationServiceInterface
{
    private const SSO_TYPE = 'google';

    /**
     * @param CmsUserDataAccess $cmsUserDataAccess
     * @param GuidCreationServiceInterface $guidService
     * @param array<string, array{cloneUserPermissionsFrom: string}> $domainToBaseUserMapping
     */
    public function __construct(
        private readonly CmsUserDataAccess $cmsUserDataAccess,
        private readonly GuidCreationServiceInterface $guidService,
        private readonly array $domainToBaseUserMapping = []
    ) {
    }

    public function register(GoogleUser $googleUser): CmsUserModel
    {
        if ($this->exists($googleUser)) {
            throw new \Exception('User already exists');
        }

        $hostedDomain = $googleUser->getHostedDomain();
        $email = $googleUser->getEmail();
        $mailDomain = mb_substr($email, mb_strpos($email, '@')+1);
        $baseUserName = $this->domainToBaseUserMapping[$hostedDomain]['cloneUserPermissionsFrom'] ?? $this->domainToBaseUserMapping[$mailDomain]['cloneUserPermissionsFrom'] ?? null;
        if (null === $baseUserName) {
            throw new \Exception('You may only register google users from the following domains: '.implode(', ', array_keys($this->domainToBaseUserMapping)));
        }
        $userId = $this->guidService->findUnusedId('cms_user');
        $user = $this->cmsUserDataAccess->loadUserByIdentifier($baseUserName)
                ->withId($userId)
                ->withUserIdentifier($googleUser->getEmail())
                ->withDateModified(new \DateTimeImmutable())
                ->withCompany($googleUser->getHostedDomain())
                ->withEmail($googleUser->getEmail())
                ->withFirstname($googleUser->getFirstName())
                ->withLastname($googleUser->getLastName())
                ->withSsoId(new CmsUserSSOModel($userId, self::SSO_TYPE, $googleUser->getId(), $this->guidService->findUnusedId('cms_user_sso')));

        return $this->cmsUserDataAccess->createUser($user);
    }

    public function update(GoogleUser $googleUser): CmsUserModel
    {
        $cmsUser = $this->getCmsUser($googleUser);

        if (null === $cmsUser) {
            throw new \Exception('User does not exist');
        }

        $newUser = $cmsUser
            ->withDateModified(new \DateTimeImmutable())
            ->withFirstname($googleUser->getFirstName())
            ->withLastname($googleUser->getLastName())
            ->withEmail($googleUser->getEmail())
            ->withCompany($googleUser->getHostedDomain())
            ->withSsoId(
                new CmsUserSSOModel(
                    $cmsUser->getId(),
                    self::SSO_TYPE,
                    $googleUser->getId(),
                    $this->guidService->findUnusedId('cms_user_sso')
                )
            );

        return $this->cmsUserDataAccess->updateUser($newUser);
    }

    public function exists(GoogleUser $googleUser): bool
    {
        return null !== $this->getCmsUser($googleUser);
    }

    private function getCmsUser(GoogleUser $googleUser): ?CmsUserModel
    {
        $existingUser = $this->cmsUserDataAccess->loadUserWithBackendLoginPermissionFromSSOID(self::SSO_TYPE, $googleUser->getId());
        if (null !== $existingUser) {
            return $existingUser;
        }

        $email = $googleUser->getEmail();

        return $this->cmsUserDataAccess->loadUserWithBackendLoginPermissionByEMail($email);
    }
}