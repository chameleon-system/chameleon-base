<?php

namespace ChameleonSystem\SecurityBundle\CmsUser;

use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class CmsUserModel implements UserInterface, PasswordAuthenticatedUserInterface
{
    public function __construct(
        readonly private string $id,
        readonly private string $userIdentifier,
        readonly private string $firstname,
        readonly private string $lastname,
        readonly private string $company,
        readonly private string $email,
        readonly private string $cmsLanguageId,
        readonly private array $availableLanguagesIsoCodes,
        readonly private string $currentEditLanguageIsoCode,
        readonly private ?string $password = null,
        readonly private array $roles = []
    ) {
    }


    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return array
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
     * @return string
     */
    public function getCurrentEditLanguageIsoCode(): string
    {
        return $this->currentEditLanguageIsoCode;
    }


}