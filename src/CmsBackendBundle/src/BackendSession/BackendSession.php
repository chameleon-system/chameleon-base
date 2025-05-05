<?php

namespace ChameleonSystem\CmsBackendBundle\BackendSession;

use ChameleonSystem\CoreBundle\Service\LanguageServiceInterface;
use ChameleonSystem\SecurityBundle\CmsUser\CmsUserModel;
use Doctrine\DBAL\Connection;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class BackendSession implements BackendSessionInterface
{
    private const SESSION_KEY = 'cmsbackend-currenteditlanguage';

    public function __construct(
        readonly private RequestStack $requestStack,
        readonly private Security $security,
        readonly private Connection $connection,
        readonly private LanguageServiceInterface $languageService
    ) {
    }

    public function getCurrentEditLanguageId(): string
    {
        $iso = $this->getCurrentEditLanguageIso6391();
        if (null === $iso) {
            return $this->languageService->getCmsBaseLanguageId();
        }
        $language = $this->languageService->getLanguageFromIsoCode($iso);
        if (null === $language) {
            return $this->languageService->getCmsBaseLanguageId();
        }

        return $language->id;
    }

    public function getCurrentEditLanguageIso6391(): ?string
    {
        $session = $this->getSession();
        if (null === $session) {
            return null;
        }

        $currentEditLanguage = $session->get(self::SESSION_KEY, null);

        if (null !== $currentEditLanguage) {
            return $currentEditLanguage;
        }

        /** @var CmsUserModel $user */
        $user = $this->security->getUser();
        if (null === $user) {
            return null;
        }

        $currentEditLanguage = $user->getCurrentEditLanguageIsoCode();

        if (null !== $currentEditLanguage) {
            return $currentEditLanguage;
        }

        $languages = $user->getAvailableEditLanguages();
        if (0 === \count($languages)) {
            return null;
        }

        $isoCodes = array_values($languages);

        return $isoCodes[0];
    }

    public function setCurrentEditLanguageIso6391(string $language): void
    {
        $session = $this->getSession();
        if (null === $session) {
            return;
        }

        $session->set(self::SESSION_KEY, $language);

        /** @var CmsUserModel $user */
        $user = $this->security->getUser();
        if (null === $user) {
            return;
        }

        $query = 'UPDATE `cms_user` SET `cms_current_edit_language` = :language WHERE `id` = :userId';
        $this->connection->executeQuery($query, ['language' => $language, 'userId' => $user->getId()]);
    }

    public function resetCurrentEditLanguage(): void
    {
        $session = $this->getSession();
        if (null === $session) {
            return;
        }

        $session->remove(self::SESSION_KEY);

        /** @var CmsUserModel $user */
        $user = $this->security->getUser();
        if (null === $user) {
            return;
        }

        $query = "UPDATE `cms_user` SET `cms_current_edit_language` = '' WHERE `id` = :userId";
        $this->connection->executeQuery($query, ['userId' => $user->getId()]);
    }

    private function getSession(): ?SessionInterface
    {
        if (false === $this->requestStack->getCurrentRequest()?->hasSession()) {
            return null;
        }

        return $this->requestStack->getCurrentRequest()?->getSession();
    }
}
