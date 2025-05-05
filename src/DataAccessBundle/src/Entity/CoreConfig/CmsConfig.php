<?php

namespace ChameleonSystem\DataAccessBundle\Entity\CoreConfig;

use ChameleonSystem\DataAccessBundle\Entity\Core\CmsIpWhitelist;
use ChameleonSystem\DataAccessBundle\Entity\Core\CmsLanguage;
use ChameleonSystem\DataAccessBundle\Entity\Core\CmsMessageManagerBackendMessage;
use ChameleonSystem\DataAccessBundle\Entity\Core\PkgCmsTheme;
use ChameleonSystem\DataAccessBundle\Entity\CorePortal\CmsPortal;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class CmsConfig
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

        // TCMSFieldPropertyTable
        /** @var Collection<int, CmsConfigImagemagick> - ImageMagick settings */
        private Collection $cmsConfigImagemagickCollection = new ArrayCollection(),
        // TCMSFieldNumber
        /** @var int - Chunk size for uploader in KB */
        private int $uploaderChunkSize = 1024,
        // TCMSFieldLookup
        /** @var CmsLanguage|null - Basic language (needed for field-based translations) */
        private ?CmsLanguage $translationBaseLanguage = null,
        // TCMSFieldLookupMultiselect
        /** @var Collection<int, CmsLanguage> - Supported languages (needed for field-based translations) */
        private Collection $cmsLanguageCollection = new ArrayCollection(),
        // TCMSFieldPropertyTable
        /** @var Collection<int, CmsConfigParameter> - Configuration settings */
        private Collection $cmsConfigParameterCollection = new ArrayCollection(),
        // TCMSFieldPropertyTable
        /** @var Collection<int, CmsMessageManagerBackendMessage> - System messages / error codes */
        private Collection $cmsMessageManagerBackendMessageCollection = new ArrayCollection(),
        // TCMSFieldVarchar
        /** @var string - SMTP server */
        private string $smtpServer = '',
        // TCMSFieldVarchar
        /** @var string - SMTP user */
        private string $smtpUser = '',
        // TCMSFieldPassword
        /** @var string - SMTP password */
        private string $smtpPassword = '',
        // TCMSFieldNumber
        /** @var int - SMTP port */
        private int $smtpPort = 25,
        // TCMSFieldPropertyTable
        /** @var Collection<int, CmsConfigCmsmoduleExtensions> - CMS module extensions */
        private Collection $cmsConfigCmsmoduleExtensionsCollection = new ArrayCollection(),
        // TCMSFieldLookup
        /** @var PkgCmsTheme|null - Backend Theme */
        private ?PkgCmsTheme $pkgCmsTheme = null,
        // TCMSFieldLookup
        /** @var CmsConfigThemes|null - CMS themes */
        private ?CmsConfigThemes $cmsConfigThemes = null,
        // TCMSFieldBoolean
        /** @var bool - Show template engine */
        private bool $showTemplateEngine = true,
        // TCMSFieldLookup
        /** @var CmsPortal|null - Main portal */
        private ?CmsPortal $cmsPortal = null,
        // TCMSFieldPropertyTable
        /** @var Collection<int, CmsIpWhitelist> - Permitted IPs */
        private Collection $cmsIpWhitelistCollection = new ArrayCollection(),
        // TCMSFieldNumber
        /** @var int - Maximum file size of file uploads (in KB) */
        private int $maxDocumentUploadSize = 409600,
        // TCMSFieldText
        /** @var string - Additional files to be deleted when clearing the cache */
        private string $additionalFilesToDeleteFromCache = '',
        // TCMSFieldVarchar
        /** @var string - CMS owner */
        private string $name = '',
        // TCMSFieldURL
        /** @var string - WYSIWYG editor CSS file */
        private string $wysiwygeditorCssUrl = '',
        // TCMSFieldNumber
        /** @var int - Maximum image file size in kb */
        private int $maxImageUploadSize = 1024,
        // TCMSFieldURL
        /** @var string - Update server */
        private string $updateServer = '',
        // TCMSFieldText
        /** @var string - List of search engines */
        private string $botlist = '',
        // TCMSFieldBoolean
        /** @var bool - Turn off all websites */
        private bool $shutdownWebsites = false,
        // TCMSFieldBoolean
        /** @var bool - Cronjobs enabled */
        private bool $cronjobsEnabled = true,
        // TCMSFieldNumber
        /** @var int - Lines per page */
        private int $entryPerPage = 0,
        // TCMSFieldNumber
        /** @var int - Database version */
        private int $databaseversion = 0,
        // TCMSFieldNumber
        /** @var int - Build no. */
        private int $build = 1
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getCmsident(): ?int
    {
        return $this->cmsident;
    }

    public function setCmsident(int $cmsident): self
    {
        $this->cmsident = $cmsident;

        return $this;
    }
    // TCMSFieldPropertyTable

    /**
     * @return Collection<int, CmsConfigImagemagick>
     */
    public function getCmsConfigImagemagickCollection(): Collection
    {
        return $this->cmsConfigImagemagickCollection;
    }

    public function addCmsConfigImagemagickCollection(CmsConfigImagemagick $cmsConfigImagemagick): self
    {
        if (!$this->cmsConfigImagemagickCollection->contains($cmsConfigImagemagick)) {
            $this->cmsConfigImagemagickCollection->add($cmsConfigImagemagick);
            $cmsConfigImagemagick->setCmsConfig($this);
        }

        return $this;
    }

    public function removeCmsConfigImagemagickCollection(CmsConfigImagemagick $cmsConfigImagemagick): self
    {
        if ($this->cmsConfigImagemagickCollection->removeElement($cmsConfigImagemagick)) {
            // set the owning side to null (unless already changed)
            if ($cmsConfigImagemagick->getCmsConfig() === $this) {
                $cmsConfigImagemagick->setCmsConfig(null);
            }
        }

        return $this;
    }

    // TCMSFieldNumber
    public function getUploaderChunkSize(): int
    {
        return $this->uploaderChunkSize;
    }

    public function setUploaderChunkSize(int $uploaderChunkSize): self
    {
        $this->uploaderChunkSize = $uploaderChunkSize;

        return $this;
    }

    // TCMSFieldLookup
    public function getTranslationBaseLanguage(): ?CmsLanguage
    {
        return $this->translationBaseLanguage;
    }

    public function setTranslationBaseLanguage(?CmsLanguage $translationBaseLanguage): self
    {
        $this->translationBaseLanguage = $translationBaseLanguage;

        return $this;
    }

    // TCMSFieldLookupMultiselect

    /**
     * @return Collection<int, CmsLanguage>
     */
    public function getCmsLanguageCollection(): Collection
    {
        return $this->cmsLanguageCollection;
    }

    public function addCmsLanguageCollection(CmsLanguage $cmsLanguageMlt): self
    {
        if (!$this->cmsLanguageCollection->contains($cmsLanguageMlt)) {
            $this->cmsLanguageCollection->add($cmsLanguageMlt);
            $cmsLanguageMlt->set($this);
        }

        return $this;
    }

    public function removeCmsLanguageCollection(CmsLanguage $cmsLanguageMlt): self
    {
        if ($this->cmsLanguageCollection->removeElement($cmsLanguageMlt)) {
            // set the owning side to null (unless already changed)
            if ($cmsLanguageMlt->get() === $this) {
                $cmsLanguageMlt->set(null);
            }
        }

        return $this;
    }

    // TCMSFieldPropertyTable

    /**
     * @return Collection<int, CmsConfigParameter>
     */
    public function getCmsConfigParameterCollection(): Collection
    {
        return $this->cmsConfigParameterCollection;
    }

    public function addCmsConfigParameterCollection(CmsConfigParameter $cmsConfigParameter): self
    {
        if (!$this->cmsConfigParameterCollection->contains($cmsConfigParameter)) {
            $this->cmsConfigParameterCollection->add($cmsConfigParameter);
            $cmsConfigParameter->setCmsConfig($this);
        }

        return $this;
    }

    public function removeCmsConfigParameterCollection(CmsConfigParameter $cmsConfigParameter): self
    {
        if ($this->cmsConfigParameterCollection->removeElement($cmsConfigParameter)) {
            // set the owning side to null (unless already changed)
            if ($cmsConfigParameter->getCmsConfig() === $this) {
                $cmsConfigParameter->setCmsConfig(null);
            }
        }

        return $this;
    }

    // TCMSFieldPropertyTable

    /**
     * @return Collection<int, CmsMessageManagerBackendMessage>
     */
    public function getCmsMessageManagerBackendMessageCollection(): Collection
    {
        return $this->cmsMessageManagerBackendMessageCollection;
    }

    public function addCmsMessageManagerBackendMessageCollection(
        CmsMessageManagerBackendMessage $cmsMessageManagerBackendMessage
    ): self {
        if (!$this->cmsMessageManagerBackendMessageCollection->contains($cmsMessageManagerBackendMessage)) {
            $this->cmsMessageManagerBackendMessageCollection->add($cmsMessageManagerBackendMessage);
            $cmsMessageManagerBackendMessage->setCmsConfig($this);
        }

        return $this;
    }

    public function removeCmsMessageManagerBackendMessageCollection(
        CmsMessageManagerBackendMessage $cmsMessageManagerBackendMessage
    ): self {
        if ($this->cmsMessageManagerBackendMessageCollection->removeElement($cmsMessageManagerBackendMessage)) {
            // set the owning side to null (unless already changed)
            if ($cmsMessageManagerBackendMessage->getCmsConfig() === $this) {
                $cmsMessageManagerBackendMessage->setCmsConfig(null);
            }
        }

        return $this;
    }

    // TCMSFieldVarchar
    public function getSmtpServer(): string
    {
        return $this->smtpServer;
    }

    public function setSmtpServer(string $smtpServer): self
    {
        $this->smtpServer = $smtpServer;

        return $this;
    }

    // TCMSFieldVarchar
    public function getSmtpUser(): string
    {
        return $this->smtpUser;
    }

    public function setSmtpUser(string $smtpUser): self
    {
        $this->smtpUser = $smtpUser;

        return $this;
    }

    // TCMSFieldPassword
    public function getSmtpPassword(): string
    {
        return $this->smtpPassword;
    }

    public function setSmtpPassword(string $smtpPassword): self
    {
        $this->smtpPassword = $smtpPassword;

        return $this;
    }

    // TCMSFieldNumber
    public function getSmtpPort(): int
    {
        return $this->smtpPort;
    }

    public function setSmtpPort(int $smtpPort): self
    {
        $this->smtpPort = $smtpPort;

        return $this;
    }

    // TCMSFieldPropertyTable

    /**
     * @return Collection<int, CmsConfigCmsmoduleExtensions>
     */
    public function getCmsConfigCmsmoduleExtensionsCollection(): Collection
    {
        return $this->cmsConfigCmsmoduleExtensionsCollection;
    }

    public function addCmsConfigCmsmoduleExtensionsCollection(CmsConfigCmsmoduleExtensions $cmsConfigCmsmoduleExtensions
    ): self {
        if (!$this->cmsConfigCmsmoduleExtensionsCollection->contains($cmsConfigCmsmoduleExtensions)) {
            $this->cmsConfigCmsmoduleExtensionsCollection->add($cmsConfigCmsmoduleExtensions);
            $cmsConfigCmsmoduleExtensions->setCmsConfig($this);
        }

        return $this;
    }

    public function removeCmsConfigCmsmoduleExtensionsCollection(
        CmsConfigCmsmoduleExtensions $cmsConfigCmsmoduleExtensions
    ): self {
        if ($this->cmsConfigCmsmoduleExtensionsCollection->removeElement($cmsConfigCmsmoduleExtensions)) {
            // set the owning side to null (unless already changed)
            if ($cmsConfigCmsmoduleExtensions->getCmsConfig() === $this) {
                $cmsConfigCmsmoduleExtensions->setCmsConfig(null);
            }
        }

        return $this;
    }

    // TCMSFieldLookup
    public function getPkgCmsTheme(): ?PkgCmsTheme
    {
        return $this->pkgCmsTheme;
    }

    public function setPkgCmsTheme(?PkgCmsTheme $pkgCmsTheme): self
    {
        $this->pkgCmsTheme = $pkgCmsTheme;

        return $this;
    }

    // TCMSFieldLookup
    public function getCmsConfigThemes(): ?CmsConfigThemes
    {
        return $this->cmsConfigThemes;
    }

    public function setCmsConfigThemes(?CmsConfigThemes $cmsConfigThemes): self
    {
        $this->cmsConfigThemes = $cmsConfigThemes;

        return $this;
    }

    // TCMSFieldBoolean
    public function isShowTemplateEngine(): bool
    {
        return $this->showTemplateEngine;
    }

    public function setShowTemplateEngine(bool $showTemplateEngine): self
    {
        $this->showTemplateEngine = $showTemplateEngine;

        return $this;
    }

    // TCMSFieldLookup
    public function getCmsPortal(): ?CmsPortal
    {
        return $this->cmsPortal;
    }

    public function setCmsPortal(?CmsPortal $cmsPortal): self
    {
        $this->cmsPortal = $cmsPortal;

        return $this;
    }

    // TCMSFieldPropertyTable

    /**
     * @return Collection<int, CmsIpWhitelist>
     */
    public function getCmsIpWhitelistCollection(): Collection
    {
        return $this->cmsIpWhitelistCollection;
    }

    public function addCmsIpWhitelistCollection(CmsIpWhitelist $cmsIpWhitelist): self
    {
        if (!$this->cmsIpWhitelistCollection->contains($cmsIpWhitelist)) {
            $this->cmsIpWhitelistCollection->add($cmsIpWhitelist);
            $cmsIpWhitelist->setCmsConfig($this);
        }

        return $this;
    }

    public function removeCmsIpWhitelistCollection(CmsIpWhitelist $cmsIpWhitelist): self
    {
        if ($this->cmsIpWhitelistCollection->removeElement($cmsIpWhitelist)) {
            // set the owning side to null (unless already changed)
            if ($cmsIpWhitelist->getCmsConfig() === $this) {
                $cmsIpWhitelist->setCmsConfig(null);
            }
        }

        return $this;
    }

    // TCMSFieldNumber
    public function getMaxDocumentUploadSize(): int
    {
        return $this->maxDocumentUploadSize;
    }

    public function setMaxDocumentUploadSize(int $maxDocumentUploadSize): self
    {
        $this->maxDocumentUploadSize = $maxDocumentUploadSize;

        return $this;
    }

    // TCMSFieldText
    public function getAdditionalFilesToDeleteFromCache(): string
    {
        return $this->additionalFilesToDeleteFromCache;
    }

    public function setAdditionalFilesToDeleteFromCache(string $additionalFilesToDeleteFromCache): self
    {
        $this->additionalFilesToDeleteFromCache = $additionalFilesToDeleteFromCache;

        return $this;
    }

    // TCMSFieldVarchar
    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    // TCMSFieldURL
    public function getWysiwygeditorCssUrl(): string
    {
        return $this->wysiwygeditorCssUrl;
    }

    public function setWysiwygeditorCssUrl(string $wysiwygeditorCssUrl): self
    {
        $this->wysiwygeditorCssUrl = $wysiwygeditorCssUrl;

        return $this;
    }

    // TCMSFieldNumber
    public function getMaxImageUploadSize(): int
    {
        return $this->maxImageUploadSize;
    }

    public function setMaxImageUploadSize(int $maxImageUploadSize): self
    {
        $this->maxImageUploadSize = $maxImageUploadSize;

        return $this;
    }

    // TCMSFieldURL
    public function getUpdateServer(): string
    {
        return $this->updateServer;
    }

    public function setUpdateServer(string $updateServer): self
    {
        $this->updateServer = $updateServer;

        return $this;
    }

    // TCMSFieldText
    public function getBotlist(): string
    {
        return $this->botlist;
    }

    public function setBotlist(string $botlist): self
    {
        $this->botlist = $botlist;

        return $this;
    }

    // TCMSFieldBoolean
    public function isShutdownWebsites(): bool
    {
        return $this->shutdownWebsites;
    }

    public function setShutdownWebsites(bool $shutdownWebsites): self
    {
        $this->shutdownWebsites = $shutdownWebsites;

        return $this;
    }

    // TCMSFieldBoolean
    public function isCronjobsEnabled(): bool
    {
        return $this->cronjobsEnabled;
    }

    public function setCronjobsEnabled(bool $cronjobsEnabled): self
    {
        $this->cronjobsEnabled = $cronjobsEnabled;

        return $this;
    }

    // TCMSFieldNumber
    public function getEntryPerPage(): int
    {
        return $this->entryPerPage;
    }

    public function setEntryPerPage(int $entryPerPage): self
    {
        $this->entryPerPage = $entryPerPage;

        return $this;
    }

    // TCMSFieldNumber
    public function getDatabaseversion(): int
    {
        return $this->databaseversion;
    }

    public function setDatabaseversion(int $databaseversion): self
    {
        $this->databaseversion = $databaseversion;

        return $this;
    }

    // TCMSFieldNumber
    public function getBuild(): int
    {
        return $this->build;
    }

    public function setBuild(int $build): self
    {
        $this->build = $build;

        return $this;
    }
}
