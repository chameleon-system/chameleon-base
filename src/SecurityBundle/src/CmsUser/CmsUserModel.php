<?php

namespace ChameleonSystem\SecurityBundle\CmsUser;

use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class CmsUserModel implements UserInterface, PasswordAuthenticatedUserInterface
{
    public function __construct(
        readonly private \DateTimeImmutable $dateModified,
        readonly private string $id,
        readonly private string $userIdentifier,
        readonly private string $firstname,
        readonly private string $lastname,
        readonly private string $company,
        readonly private string $email,
        readonly private string $cmsLanguageId,
        readonly private array $availableLanguagesIsoCodes,
        readonly private ?string $currentEditLanguageIsoCode,
        readonly private array $availableEditLanguages,
        readonly private ?string $password = null,
        readonly private array $roles = [],
        readonly private array $rights = [],
        readonly private array $groups = [],
        readonly private array $portals = []
    ) {
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getDateModified(): \DateTimeImmutable
    {
        return $this->dateModified;
    }

    /**
     * returns users portals. key is id, value is CMS_PORTAL_<external_identifier>
     * @return array<string, string>
     */
    public function getPortals(): array
    {
        return $this->portals;
    }


    /**
     * List of the users groups - key is the group id, value the CMS_GROUP_<internal_name>
     * @return array<string,string>
     */
    public function getGroups(): array
    {
        return $this->groups;
    }

    /**
     * List of users right - key is the permission id, the value IS CMS_RIGHT_<name>
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


    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     *
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
     * If you store any temporary, sensitive data on the user, clear it here
     */
    public function eraseCredentials(): void
    {
        // TODO: Implement eraseCredentials() method.
    }

    /**
     * @deprecated since Symfony 5.3
     */
    public function getUsername(): string
    {
        return $this->getUserIdentifier();
    }

    /**
     * @return array<string>
     */
    public function getAvailableLanguagesIsoCodes(): array
    {
        return $this->availableLanguagesIsoCodes;
    }

    /**
     * @return string
     */
    public function getFirstname(): string
    {
        return $this->firstname;
    }

    /**
     * @return string
     */
    public function getLastname(): string
    {
        return $this->lastname;
    }

    /**
     * @return string
     */
    public function getCompany(): string
    {
        return $this->company;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
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


}