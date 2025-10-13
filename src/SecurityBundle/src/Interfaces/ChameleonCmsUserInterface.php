<?php

namespace ChameleonSystem\SecurityBundle\Interfaces;

use ChameleonSystem\SecurityBundle\CmsUser\CmsUserSSOModel;
use Scheb\TwoFactorBundle\Model\Google\TwoFactorInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

interface ChameleonCmsUserInterface extends UserInterface, PasswordAuthenticatedUserInterface, TwoFactorInterface
{
    /**
     * @return CmsUserSSOModel[]
     */
    public function getSsoIds(): array;

    public function getDateModified(): \DateTimeImmutable;

    /**
     * returns users portals. key is id, value is CMS_PORTAL_<external_identifier>.
     *
     * @return array<string, string>
     */
    public function getPortals(): array;

    /**
     * List of the users groups - key is the group id, value the CMS_GROUP_<internal_name>.
     *
     * @return array<string, string>
     */
    public function getGroups(): array;

    /**
     * comma separted id list, single quoted for queries for IN(...).
     * for example: `'123','456'`, otherwise returns `null`
     */
    public function getGroupsForSqlQuery(): ?string;

    /**
     * List of users right - key is the permission id, the value IS CMS_RIGHT_<name>.
     *
     * @return array<string, string>
     */
    public function getRights(): array;

    /**
     * @return array<string, string> assoc array with key being language id and value being language iso code
     */
    public function getAvailableEditLanguages(): array;

    public function getId(): string;

    /**
     * @return array<string, string> key is the id, name is ROLE_<name>
     */
    public function getRoles(): array;

    public function getUserIdentifier(): string;

    public function getPassword(): ?string;

    public function getSalt(): ?string;

    public function eraseCredentials(): void;

    /**
     * @return array<string>
     */
    public function getAvailableLanguagesIsoCodes(): array;

    public function getFirstname(): string;

    public function getLastname(): string;

    public function getCompany(): string;

    public function getEmail(): string;

    public function getCmsLanguageId(): string;

    public function getCurrentEditLanguageIsoCode(): ?string;

    public function getDashboardWidgetConfig(): string;

    // --- TwoFactorInterface (scheb/2fa) ---
    public function isGoogleAuthenticatorEnabled(): bool;

    public function getGoogleAuthenticatorUsername(): string;

    public function getGoogleAuthenticatorSecret(): ?string;

    // --- Immutables / Fluent "with*" Kopierer ---
    public function withGoogleAuthenticatorSecret(string $secret): self;

    public function withId(string $id): self;

    public function withDateModified(\DateTimeImmutable $dateTime): self;

    public function withUserIdentifier(string $userIdentifier): self;

    public function withSsoId(CmsUserSSOModel $SSOModel): self;

    public function withFirstname(string $firstName): self;

    public function withLastname(string $lastName): self;

    public function withEmail(string $email): self;

    public function withCompany(string $company): self;
}
