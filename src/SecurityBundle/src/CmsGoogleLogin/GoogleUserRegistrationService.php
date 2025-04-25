<?php

namespace ChameleonSystem\SecurityBundle\CmsGoogleLogin;

use ChameleonSystem\CoreBundle\Exception\GuidCreationFailedException;
use ChameleonSystem\CoreBundle\Interfaces\GuidCreationServiceInterface;
use ChameleonSystem\SecurityBundle\CmsUser\CmsUserDataAccess;
use ChameleonSystem\SecurityBundle\CmsUser\CmsUserModel;
use ChameleonSystem\SecurityBundle\CmsUser\CmsUserSSOModel;
use ChameleonSystem\SecurityBundle\Exception\RegisterUserErrorException;
use ChameleonSystem\SecurityBundle\Exception\UpdateUserErrorException;
use League\OAuth2\Client\Provider\GoogleUser;

class GoogleUserRegistrationService implements GoogleUserRegistrationServiceInterface
{
    private const SSO_TYPE = 'google';

    /**
     * @param CmsUserDataAccess $cmsUserDataAccess
     * @param GuidCreationServiceInterface $guidService
     * @param array<string, array{clone_user_permissions_from: string}> $domainToBaseUserMapping
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
            throw new RegisterUserErrorException('User already exists');
        }

        $hostedDomain = $googleUser->getHostedDomain();
        $email = $googleUser->getEmail();
        $mailDomain = mb_substr($email, mb_strpos($email, '@')+1);
        $baseUserName = $this->domainToBaseUserMapping[$hostedDomain]['clone_user_permissions_from'] ?? $this->domainToBaseUserMapping[$mailDomain]['clone_user_permissions_from'] ?? null;
        if (null === $baseUserName) {
            throw new RegisterUserErrorException('You may only register google users from the following domains: '.implode(', ', array_keys($this->domainToBaseUserMapping)));
        }
        try {
            $userId = $this->guidService->findUnusedId('cms_user');
        } catch (GuidCreationFailedException $e) {
            throw new RegisterUserErrorException('Failed to register user - unable to obtain a uuid for the cms_user table.', 0, $e);
        }

        try {
        $user = $this->cmsUserDataAccess->loadUserByIdentifier($baseUserName)
                ->withId($userId)
                ->withUserIdentifier($googleUser->getEmail())
                ->withDateModified(new \DateTimeImmutable())
                ->withCompany($googleUser->getHostedDomain())
                ->withEmail($googleUser->getEmail())
                ->withFirstname($googleUser->getFirstName())
                ->withLastname($googleUser->getLastName())
                ->withSsoId(new CmsUserSSOModel($userId, self::SSO_TYPE, $googleUser->getId(), $this->guidService->findUnusedId('cms_user_sso')));
        } catch (\Throwable $e) {
            throw new RegisterUserErrorException('Failed to register user - unable to load base user: '.$baseUserName, 0, $e);
        }

        try {
            return $this->cmsUserDataAccess->createUser($user);
        } catch (\Throwable $e) {
            throw new RegisterUserErrorException('Failed to register user - unable to write user to database.', 0, $e);
        }
    }

    public function update(GoogleUser $googleUser): CmsUserModel
    {
        $cmsUser = $this->getCmsUser($googleUser);

        if (null === $cmsUser) {
            throw new UpdateUserErrorException(sprintf('Failed to update user from google user [%s] - User does not exist', $googleUser->getEmail()));
        }

        try {
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
        } catch (GuidCreationFailedException $e) {
            throw new UpdateUserErrorException('Failed to update user - unable to obtain a uuid for the cms_user_sso table.', 0, $e);
        }

        try {
            return $this->cmsUserDataAccess->updateUser($newUser);
        } catch (\Throwable $e) {
            throw new UpdateUserErrorException('Failed to update user - unable to write user to database.', 0, $e);
        }
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