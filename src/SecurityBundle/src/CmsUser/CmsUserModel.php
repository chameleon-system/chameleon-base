<?php

namespace ChameleonSystem\SecurityBundle\CmsUser;

use Scheb\TwoFactorBundle\Model\Google\TwoFactorInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class CmsUserModel implements UserInterface, PasswordAuthenticatedUserInterface, TwoFactorInterface
{
    /**
     * @param array<string> $availableLanguagesIsoCodes
     * @param array<string, string> $availableEditLanguages
     * @param array<string,string> $roles
     * @param array<string,string> $rights
     * @param array<string,string> $groups
     * @param array<string, string> $portals
     * @param CmsUserSSOModel[] $ssoIds
     */
    public function __construct(
        private \DateTimeImmutable $dateModified,
        private string $id,
        private string $userIdentifier,
        private string $firstname,
        private string $lastname,
        private string $company,
        private string $email,
        readonly private string $cmsLanguageId,
        readonly private array $availableLanguagesIsoCodes,
        readonly private ?string $currentEditLanguageIsoCode,
        readonly private array $availableEditLanguages,
        readonly private ?string $password = null,
        readonly private array $roles = [],
        readonly private array $rights = [],
        readonly private array $groups = [],
        readonly private array $portals = [],
        private array $ssoIds = [],
        private ?string $googleAuthenticatorSecret = null
    ) {
    }

    /**
     * @return CmsUserSSOModel[]
     */
    public function getSsoIds(): array
    {
        return $this->ssoIds;
    }

    public function getDateModified(): \DateTimeImmutable
    {
        return $this->dateModified;
    }

    /**
     * returns users portals. key is id, value is CMS_PORTAL_<external_identifier>.
     *
     * @return array<string, string>
     */
    public function getPortals(): array
    {
        return $this->portals;
    }

    /**
     * List of the users groups - key is the group id, value the CMS_GROUP_<internal_name>.
     *
     * @return array<string,string>
     */
    public function getGroups(): array
    {
        return $this->groups;
    }

    /**
     * List of users right - key is the permission id, the value IS CMS_RIGHT_<name>.
     *
     * @return array<string,string>
     */
    public function getRights(): array
    {
        return $this->rights;
    }

    /**
     * @return array<string, string> - assoc array with key being langauge id and value being language iso code
     */
    public function getAvailableEditLanguages(): array
    {
        return $this->availableEditLanguages;
    }

    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return array<string, string> key is the id, name is ROLE_<name>
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getUserIdentifier(): string
    {
        return $this->userIdentifier;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * If you store any temporary, sensitive data on the user, clear it here.
     */
    public function eraseCredentials(): void
    {
        // TODO: Implement eraseCredentials() method.
    }

    /**
     * @return array<string>
     */
    public function getAvailableLanguagesIsoCodes(): array
    {
        return $this->availableLanguagesIsoCodes;
    }

    public function getFirstname(): string
    {
        return $this->firstname;
    }

    public function getLastname(): string
    {
        return $this->lastname;
    }

    public function getCompany(): string
    {
        return $this->company;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getCmsLanguageId(): string
    {
        return $this->cmsLanguageId;
    }

    /**
     * @return ?string
     */
    public function getCurrentEditLanguageIsoCode(): ?string
    {
        return $this->currentEditLanguageIsoCode;
    }

    public function isGoogleAuthenticatorEnabled(): bool
    {
        return '' !== $this->googleAuthenticatorSecret;
    }

    public function getGoogleAuthenticatorUsername(): string
    {
        return $this->email;
    }

    public function getGoogleAuthenticatorSecret(): ?string
    {
        return $this->googleAuthenticatorSecret;
    }

    public function withGoogleAuthenticatorSecret(string $secret): self
    {
        $user = clone $this;
        $user->googleAuthenticatorSecret = $secret;

        return $user;
    }

    public function withId(string $id): self
    {
        $user = clone $this;
        $user->id = $id;

        return $user;
    }

    public function withDateModified(\DateTimeImmutable $dateTime): self
    {
        $user = clone $this;
        $user->dateModified = $dateTime;

        return $user;
    }

    public function withUserIdentifier(string $userIdentifier): self
    {
        $user = clone $this;
        $user->userIdentifier = $userIdentifier;

        return $user;
    }

    public function withSsoId(CmsUserSSOModel $SSOModel): self
    {
        $user = clone $this;
        foreach ($this->ssoIds as $key => $ssId) {
            if (
                $SSOModel->getType() !== $ssId->getType()
                || $SSOModel->getSsoId() !== $ssId->getSsoId()
            ) {
                continue;
            }

            // exact match. no need to add.
            return $user;
        }
        $user->ssoIds[] = $SSOModel;

        return $user;
    }

    public function withFirstname(string $firstName): self
    {
        $user = clone $this;
        $user->firstname = $firstName;

        return $user;
    }

    public function withLastname(string $lastName): self
    {
        $user = clone $this;
        $user->lastname = $lastName;

        return $user;
    }

    public function withEmail(string $email): self
    {
        $user = clone $this;
        $user->email = $email;

        return $user;
    }

    public function withCompany(string $company): self
    {
        $user = clone $this;
        $user->company = $company;

        return $user;
    }
}
