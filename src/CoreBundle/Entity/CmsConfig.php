<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsConfig {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsLanguage|null - Basic language (needed for field-based translations) */
private \ChameleonSystem\CoreBundle\Entity\CmsLanguage|null $translationBaseLanguage = null,
/** @var null|string - Basic language (needed for field-based translations) */
private ?string $translationBaseLanguageId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\PkgCmsTheme|null - Backend Theme */
private \ChameleonSystem\CoreBundle\Entity\PkgCmsTheme|null $pkgCmsTheme = null,
/** @var null|string - Backend Theme */
private ?string $pkgCmsThemeId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsConfigThemes|null - CMS themes */
private \ChameleonSystem\CoreBundle\Entity\CmsConfigThemes|null $cmsConfigThemes = null,
/** @var null|string - CMS themes */
private ?string $cmsConfigThemesId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsPortal|null - Main portal */
private \ChameleonSystem\CoreBundle\Entity\CmsPortal|null $cmsPortal = null,
/** @var null|string - Main portal */
private ?string $cmsPortalId = null
, 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\CmsConfigImagemagick[] - ImageMagick settings */
private \Doctrine\Common\Collections\Collection $cmsConfigImagemagickCollection = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldNumber
/** @var int - Chunk size for uploader in KB */
private int $uploaderChunkSize = 1024, 
    // TCMSFieldLookupMultiselect
/** @var \ChameleonSystem\CoreBundle\Entity\CmsLanguage[] - Supported languages (needed for field-based translations) */
private \Doctrine\Common\Collections\Collection $cmsLanguageMlt = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\CmsConfigParameter[] - Configuration settings */
private \Doctrine\Common\Collections\Collection $cmsConfigParameterCollection = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\CmsMessageManagerBackendMessage[] - System messages / error codes */
private \Doctrine\Common\Collections\Collection $cmsMessageManagerBackendMessageCollection = new \Doctrine\Common\Collections\ArrayCollection(), 
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
/** @var \ChameleonSystem\CoreBundle\Entity\CmsConfigCmsmoduleExtensions[] - CMS module extensions */
private \Doctrine\Common\Collections\Collection $cmsConfigCmsmoduleExtensionsCollection = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldBoolean
/** @var bool - Show template engine */
private bool $showTemplateEngine = true, 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\CmsIpWhitelist[] - Permitted IPs */
private \Doctrine\Common\Collections\Collection $cmsIpWhitelistCollection = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldNumber
/** @var int - Maximum file size of file uploads (in KB)  */
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
private int $build = 1  ) {}

  public function getId(): ?string
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
public function getCmsConfigImagemagickCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->cmsConfigImagemagickCollection;
}
public function setCmsConfigImagemagickCollection(\Doctrine\Common\Collections\Collection $cmsConfigImagemagickCollection): self
{
    $this->cmsConfigImagemagickCollection = $cmsConfigImagemagickCollection;

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
public function getTranslationBaseLanguage(): \ChameleonSystem\CoreBundle\Entity\CmsLanguage|null
{
    return $this->translationBaseLanguage;
}
public function setTranslationBaseLanguage(\ChameleonSystem\CoreBundle\Entity\CmsLanguage|null $translationBaseLanguage): self
{
    $this->translationBaseLanguage = $translationBaseLanguage;
    $this->translationBaseLanguageId = $translationBaseLanguage?->getId();

    return $this;
}
public function getTranslationBaseLanguageId(): ?string
{
    return $this->translationBaseLanguageId;
}
public function setTranslationBaseLanguageId(?string $translationBaseLanguageId): self
{
    $this->translationBaseLanguageId = $translationBaseLanguageId;
    // todo - load new id
    //$this->translationBaseLanguageId = $?->getId();

    return $this;
}



  
    // TCMSFieldLookupMultiselect
public function getCmsLanguageMlt(): \Doctrine\Common\Collections\Collection
{
    return $this->cmsLanguageMlt;
}
public function setCmsLanguageMlt(\Doctrine\Common\Collections\Collection $cmsLanguageMlt): self
{
    $this->cmsLanguageMlt = $cmsLanguageMlt;

    return $this;
}


  
    // TCMSFieldPropertyTable
public function getCmsConfigParameterCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->cmsConfigParameterCollection;
}
public function setCmsConfigParameterCollection(\Doctrine\Common\Collections\Collection $cmsConfigParameterCollection): self
{
    $this->cmsConfigParameterCollection = $cmsConfigParameterCollection;

    return $this;
}


  
    // TCMSFieldPropertyTable
public function getCmsMessageManagerBackendMessageCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->cmsMessageManagerBackendMessageCollection;
}
public function setCmsMessageManagerBackendMessageCollection(\Doctrine\Common\Collections\Collection $cmsMessageManagerBackendMessageCollection): self
{
    $this->cmsMessageManagerBackendMessageCollection = $cmsMessageManagerBackendMessageCollection;

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
public function getCmsConfigCmsmoduleExtensionsCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->cmsConfigCmsmoduleExtensionsCollection;
}
public function setCmsConfigCmsmoduleExtensionsCollection(\Doctrine\Common\Collections\Collection $cmsConfigCmsmoduleExtensionsCollection): self
{
    $this->cmsConfigCmsmoduleExtensionsCollection = $cmsConfigCmsmoduleExtensionsCollection;

    return $this;
}


  
    // TCMSFieldLookup
public function getPkgCmsTheme(): \ChameleonSystem\CoreBundle\Entity\PkgCmsTheme|null
{
    return $this->pkgCmsTheme;
}
public function setPkgCmsTheme(\ChameleonSystem\CoreBundle\Entity\PkgCmsTheme|null $pkgCmsTheme): self
{
    $this->pkgCmsTheme = $pkgCmsTheme;
    $this->pkgCmsThemeId = $pkgCmsTheme?->getId();

    return $this;
}
public function getPkgCmsThemeId(): ?string
{
    return $this->pkgCmsThemeId;
}
public function setPkgCmsThemeId(?string $pkgCmsThemeId): self
{
    $this->pkgCmsThemeId = $pkgCmsThemeId;
    // todo - load new id
    //$this->pkgCmsThemeId = $?->getId();

    return $this;
}



  
    // TCMSFieldLookup
public function getCmsConfigThemes(): \ChameleonSystem\CoreBundle\Entity\CmsConfigThemes|null
{
    return $this->cmsConfigThemes;
}
public function setCmsConfigThemes(\ChameleonSystem\CoreBundle\Entity\CmsConfigThemes|null $cmsConfigThemes): self
{
    $this->cmsConfigThemes = $cmsConfigThemes;
    $this->cmsConfigThemesId = $cmsConfigThemes?->getId();

    return $this;
}
public function getCmsConfigThemesId(): ?string
{
    return $this->cmsConfigThemesId;
}
public function setCmsConfigThemesId(?string $cmsConfigThemesId): self
{
    $this->cmsConfigThemesId = $cmsConfigThemesId;
    // todo - load new id
    //$this->cmsConfigThemesId = $?->getId();

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
public function getCmsPortal(): \ChameleonSystem\CoreBundle\Entity\CmsPortal|null
{
    return $this->cmsPortal;
}
public function setCmsPortal(\ChameleonSystem\CoreBundle\Entity\CmsPortal|null $cmsPortal): self
{
    $this->cmsPortal = $cmsPortal;
    $this->cmsPortalId = $cmsPortal?->getId();

    return $this;
}
public function getCmsPortalId(): ?string
{
    return $this->cmsPortalId;
}
public function setCmsPortalId(?string $cmsPortalId): self
{
    $this->cmsPortalId = $cmsPortalId;
    // todo - load new id
    //$this->cmsPortalId = $?->getId();

    return $this;
}



  
    // TCMSFieldPropertyTable
public function getCmsIpWhitelistCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->cmsIpWhitelistCollection;
}
public function setCmsIpWhitelistCollection(\Doctrine\Common\Collections\Collection $cmsIpWhitelistCollection): self
{
    $this->cmsIpWhitelistCollection = $cmsIpWhitelistCollection;

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
