<?php

namespace ChameleonSystem\SecurityBundle\CmsGoogleLogin;

use ChameleonSystem\CoreBundle\Interfaces\GuidCreationServiceInterface;
use ChameleonSystem\SecurityBundle\CmsUser\CmsUserDataAccess;
use ChameleonSystem\SecurityBundle\CmsUser\CmsUserModel;
use League\OAuth2\Client\Provider\GoogleUser;

class GoogleUserRegistrationService implements GoogleUserRegistrationServiceInterface
{
    public function __construct(
        private readonly CmsUserDataAccess $cmsUserDataAccess,
        private readonly GuidCreationServiceInterface $guidService,
        private readonly array $allowedDomains = []
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
        $baseUserName = $this->allowedDomains[$hostedDomain] ?? $this->allowedDomains[$mailDomain] ?? null;
        if (null === $baseUserName) {
            throw new \Exception('You may only register google users from the following domains: '.implode(', ', array_keys($this->allowedDomains)));
        }
        $user = $this->cmsUserDataAccess->loadUserByIdentifier($baseUserName)
                ->withId($this->guidService->findUnusedId('cms_user'))
                ->withUserIdentifier($googleUser->getEmail())
                ->withDateModified(new \DateTimeImmutable())
                ->withCompany($googleUser->getHostedDomain())
                ->withEmail($googleUser->getEmail())
                ->withFirstname($googleUser->getFirstName())
                ->withLastname($googleUser->getLastName())
                ->withGoogleId($googleUser->getId());

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
            ->withGoogleId($googleUser->getId())
            ->withFirstname($googleUser->getFirstName())
            ->withLastname($googleUser->getLastName())
            ->withEmail($googleUser->getEmail())
            ->withCompany($googleUser->getHostedDomain())
        ;

        return $this->cmsUserDataAccess->updateUser($newUser);
    }

    public function exists(GoogleUser $googleUser): bool
    {
        return null !== $this->getCmsUser($googleUser);
    }

    private function getCmsUser(GoogleUser $googleUser): ?CmsUserModel
    {
        $existingUser = $this->cmsUserDataAccess->loadUserByGoogleToken($googleUser->getId());
        if (null !== $existingUser) {
            return $existingUser;
        }

        $email = $googleUser->getEmail();

        return $this->cmsUserDataAccess->loadUserByEMail($email);
    }

    private function getDefault(string $key): string|array
    {
        $value = $this->allowedDomains[$key] ?? null;
        if (null !== $value) {
            return $value;
        }

        throw new \Exception('Missing config value for '.$key.' in newUserConfig');
    }

}