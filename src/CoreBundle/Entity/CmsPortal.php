<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsPortal {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsLanguage|null - Portal language */
private \ChameleonSystem\CoreBundle\Entity\CmsLanguage|null $cmsLanguage = null,
/** @var null|string - Portal language */
private ?string $cmsLanguageId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsMedia|null - Logo */
private \ChameleonSystem\CoreBundle\Entity\CmsMedia|null $images = null,
/** @var null|string - Logo */
private ?string $imagesId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsMedia|null - Logo for watermarking */
private \ChameleonSystem\CoreBundle\Entity\CmsMedia|null $watermarkLogo = null,
/** @var null|string - Logo for watermarking */
private ?string $watermarkLogoId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsMedia|null - Background image */
private \ChameleonSystem\CoreBundle\Entity\CmsMedia|null $backgroundImage = null,
/** @var null|string - Background image */
private ?string $backgroundImageId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsLocals|null - Locale */
private \ChameleonSystem\CoreBundle\Entity\CmsLocals|null $cmsLocals = null,
/** @var null|string - Locale */
private ?string $cmsLocalsId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\PkgCmsTheme|null - Website presentation / theme */
private \ChameleonSystem\CoreBundle\Entity\PkgCmsTheme|null $pkgCmsTheme = null,
/** @var null|string - Website presentation / theme */
private ?string $pkgCmsThemeId = null
, 
    // TCMSFieldVarchar
/** @var string - Name */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - Portal title */
private string $title = '', 
    // TCMSFieldVarchar
/** @var string - Identifier / prefix */
private string $identifier = '', 
    // TCMSFieldVarchar
/** @var string - External portal name */
private string $externalIdentifier = '', 
    // TCMSFieldBoolean
/** @var bool - Enable multi-language ability */
private bool $useMultilanguage = false, 
    // TCMSFieldBoolean
/** @var bool - Show untranslated links */
private bool $showNotTanslated = false, 
    // ChameleonSystem\CoreBundle\Field\FieldTreeNodePortalSelect
/** @var \ChameleonSystem\CoreBundle\Entity\CmsTree - Navigation start node */
private \ChameleonSystem\CoreBundle\Entity\CmsTree|null $mainNodeTree = null, 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\CmsPortalNavigation[] - Navigations */
private \Doctrine\Common\Collections\Collection $propertyNavigationsCollection = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldPortalHomeTreeNode
/** @var \ChameleonSystem\CoreBundle\Entity\CmsTree - Portal home page */
private \ChameleonSystem\CoreBundle\Entity\CmsTree|null $homeNodeId = null, 
    // TCMSFieldPortalHomeTreeNode
/** @var \ChameleonSystem\CoreBundle\Entity\CmsTree - Page not found */
private \ChameleonSystem\CoreBundle\Entity\CmsTree|null $pageNotFoundNode = null, 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\CmsDivision[] - Sections */
private \Doctrine\Common\Collections\Collection $cmsPortalDivisionsCollection = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldPosition
/** @var int - Sorting */
private int $sortOrder = 0, 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\CmsPortalDomains[] - Domains */
private \Doctrine\Common\Collections\Collection $cmsPortalDomainsCollection = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldURL
/** @var string - Favicon URL */
private string $faviconUrl = '/favicon.ico', 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\CmsMessageManagerMessage[] - System messages / error codes */
private \Doctrine\Common\Collections\Collection $cmsMessageManagerMessageCollection = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\CmsPortalSystemPage[] - System pages */
private \Doctrine\Common\Collections\Collection $cmsPortalSystemPageCollection = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldLookupMultiselectCheckboxesPossibleLanguages
/** @var \ChameleonSystem\CoreBundle\Entity\CmsLanguage[] - Portal languages */
private \Doctrine\Common\Collections\Collection $cmsLanguageMlt = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldBoolean
/** @var bool - Google sitemap */
private bool $useGooglesitemap = true, 
    // TCMSFieldVarchar
/** @var string - Short description */
private string $metaDescription = '', 
    // TCMSFieldText
/** @var string - Search terms */
private string $metaKeywords = '', 
    // TCMSFieldVarchar
/** @var string - Author */
private string $metaAuthor = '', 
    // TCMSFieldVarchar
/** @var string - Publisher */
private string $metaPublisher = '', 
    // TCMSFieldText
/** @var string - Your meta data */
private string $customMetadata = '', 
    // TCMSFieldText
/** @var string - Action-Plugins */
private string $actionPluginList = '', 
    // TCMSFieldVarchar
/** @var string - Google Analytics ID */
private string $googleAnalyticNumber = '', 
    // TCMSFieldVarchar
/** @var string - etracker ID */
private string $etrackerId = '', 
    // TCMSFieldVarchar
/** @var string - IVW ID */
private string $ivwId = '', 
    // TCMSFieldBoolean
/** @var bool - Include in search index generation */
private bool $indexSearch = true, 
    // TCMSFieldBoolean
/** @var bool - Use / instead of .html in SEO URLs */
private bool $useSlashInSeoUrls = false, 
    // TCMSFieldBoolean
/** @var bool - Deactivate portal */
private bool $deactivePortal = false, 
    // TCMSFieldVarchar
/** @var string - WYSIWYG text editor CSS URL */
private string $wysiwygCssUrl = '', 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\CmsUrlAlias[] - URL alias list */
private \Doctrine\Common\Collections\Collection $cmsUrlAliasCollection = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldText
/** @var string - robots.txt */
private string $robots = ''  ) {}

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


  
    // TCMSFieldVarchar
public function getTitle(): string
{
    return $this->title;
}
public function setTitle(string $title): self
{
    $this->title = $title;

    return $this;
}


  
    // TCMSFieldVarchar
public function getIdentifier(): string
{
    return $this->identifier;
}
public function setIdentifier(string $identifier): self
{
    $this->identifier = $identifier;

    return $this;
}


  
    // TCMSFieldVarchar
public function getExternalIdentifier(): string
{
    return $this->externalIdentifier;
}
public function setExternalIdentifier(string $externalIdentifier): self
{
    $this->externalIdentifier = $externalIdentifier;

    return $this;
}


  
    // TCMSFieldLookup
public function getCmsLanguage(): \ChameleonSystem\CoreBundle\Entity\CmsLanguage|null
{
    return $this->cmsLanguage;
}
public function setCmsLanguage(\ChameleonSystem\CoreBundle\Entity\CmsLanguage|null $cmsLanguage): self
{
    $this->cmsLanguage = $cmsLanguage;
    $this->cmsLanguageId = $cmsLanguage?->getId();

    return $this;
}
public function getCmsLanguageId(): ?string
{
    return $this->cmsLanguageId;
}
public function setCmsLanguageId(?string $cmsLanguageId): self
{
    $this->cmsLanguageId = $cmsLanguageId;
    // todo - load new id
    //$this->cmsLanguageId = $?->getId();

    return $this;
}



  
    // TCMSFieldBoolean
public function isUseMultilanguage(): bool
{
    return $this->useMultilanguage;
}
public function setUseMultilanguage(bool $useMultilanguage): self
{
    $this->useMultilanguage = $useMultilanguage;

    return $this;
}


  
    // TCMSFieldBoolean
public function isShowNotTanslated(): bool
{
    return $this->showNotTanslated;
}
public function setShowNotTanslated(bool $showNotTanslated): self
{
    $this->showNotTanslated = $showNotTanslated;

    return $this;
}


  
    // ChameleonSystem\CoreBundle\Field\FieldTreeNodePortalSelect
public function getMainNodeTree(): \ChameleonSystem\CoreBundle\Entity\CmsTree|null
{
    return $this->mainNodeTree;
}
public function setMainNodeTree(\ChameleonSystem\CoreBundle\Entity\CmsTree|null $mainNodeTree): self
{
    $this->mainNodeTree = $mainNodeTree;

    return $this;
}


  
    // TCMSFieldPropertyTable
public function getPropertyNavigationsCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->propertyNavigationsCollection;
}
public function setPropertyNavigationsCollection(\Doctrine\Common\Collections\Collection $propertyNavigationsCollection): self
{
    $this->propertyNavigationsCollection = $propertyNavigationsCollection;

    return $this;
}


  
    // TCMSFieldPortalHomeTreeNode
public function getHomeNodeId(): \ChameleonSystem\CoreBundle\Entity\CmsTree|null
{
    return $this->homeNodeId;
}
public function setHomeNodeId(\ChameleonSystem\CoreBundle\Entity\CmsTree|null $homeNodeId): self
{
    $this->homeNodeId = $homeNodeId;

    return $this;
}


  
    // TCMSFieldPortalHomeTreeNode
public function getPageNotFoundNode(): \ChameleonSystem\CoreBundle\Entity\CmsTree|null
{
    return $this->pageNotFoundNode;
}
public function setPageNotFoundNode(\ChameleonSystem\CoreBundle\Entity\CmsTree|null $pageNotFoundNode): self
{
    $this->pageNotFoundNode = $pageNotFoundNode;

    return $this;
}


  
    // TCMSFieldPropertyTable
public function getCmsPortalDivisionsCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->cmsPortalDivisionsCollection;
}
public function setCmsPortalDivisionsCollection(\Doctrine\Common\Collections\Collection $cmsPortalDivisionsCollection): self
{
    $this->cmsPortalDivisionsCollection = $cmsPortalDivisionsCollection;

    return $this;
}


  
    // TCMSFieldPosition
public function getSortOrder(): int
{
    return $this->sortOrder;
}
public function setSortOrder(int $sortOrder): self
{
    $this->sortOrder = $sortOrder;

    return $this;
}


  
    // TCMSFieldPropertyTable
public function getCmsPortalDomainsCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->cmsPortalDomainsCollection;
}
public function setCmsPortalDomainsCollection(\Doctrine\Common\Collections\Collection $cmsPortalDomainsCollection): self
{
    $this->cmsPortalDomainsCollection = $cmsPortalDomainsCollection;

    return $this;
}


  
    // TCMSFieldURL
public function getFaviconUrl(): string
{
    return $this->faviconUrl;
}
public function setFaviconUrl(string $faviconUrl): self
{
    $this->faviconUrl = $faviconUrl;

    return $this;
}


  
    // TCMSFieldLookup
public function getImages(): \ChameleonSystem\CoreBundle\Entity\CmsMedia|null
{
    return $this->images;
}
public function setImages(\ChameleonSystem\CoreBundle\Entity\CmsMedia|null $images): self
{
    $this->images = $images;
    $this->imagesId = $images?->getId();

    return $this;
}
public function getImagesId(): ?string
{
    return $this->imagesId;
}
public function setImagesId(?string $imagesId): self
{
    $this->imagesId = $imagesId;
    // todo - load new id
    //$this->imagesId = $?->getId();

    return $this;
}



  
    // TCMSFieldLookup
public function getWatermarkLogo(): \ChameleonSystem\CoreBundle\Entity\CmsMedia|null
{
    return $this->watermarkLogo;
}
public function setWatermarkLogo(\ChameleonSystem\CoreBundle\Entity\CmsMedia|null $watermarkLogo): self
{
    $this->watermarkLogo = $watermarkLogo;
    $this->watermarkLogoId = $watermarkLogo?->getId();

    return $this;
}
public function getWatermarkLogoId(): ?string
{
    return $this->watermarkLogoId;
}
public function setWatermarkLogoId(?string $watermarkLogoId): self
{
    $this->watermarkLogoId = $watermarkLogoId;
    // todo - load new id
    //$this->watermarkLogoId = $?->getId();

    return $this;
}



  
    // TCMSFieldLookup
public function getBackgroundImage(): \ChameleonSystem\CoreBundle\Entity\CmsMedia|null
{
    return $this->backgroundImage;
}
public function setBackgroundImage(\ChameleonSystem\CoreBundle\Entity\CmsMedia|null $backgroundImage): self
{
    $this->backgroundImage = $backgroundImage;
    $this->backgroundImageId = $backgroundImage?->getId();

    return $this;
}
public function getBackgroundImageId(): ?string
{
    return $this->backgroundImageId;
}
public function setBackgroundImageId(?string $backgroundImageId): self
{
    $this->backgroundImageId = $backgroundImageId;
    // todo - load new id
    //$this->backgroundImageId = $?->getId();

    return $this;
}



  
    // TCMSFieldPropertyTable
public function getCmsMessageManagerMessageCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->cmsMessageManagerMessageCollection;
}
public function setCmsMessageManagerMessageCollection(\Doctrine\Common\Collections\Collection $cmsMessageManagerMessageCollection): self
{
    $this->cmsMessageManagerMessageCollection = $cmsMessageManagerMessageCollection;

    return $this;
}


  
    // TCMSFieldPropertyTable
public function getCmsPortalSystemPageCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->cmsPortalSystemPageCollection;
}
public function setCmsPortalSystemPageCollection(\Doctrine\Common\Collections\Collection $cmsPortalSystemPageCollection): self
{
    $this->cmsPortalSystemPageCollection = $cmsPortalSystemPageCollection;

    return $this;
}


  
    // TCMSFieldLookupMultiselectCheckboxesPossibleLanguages
public function getCmsLanguageMlt(): \Doctrine\Common\Collections\Collection
{
    return $this->cmsLanguageMlt;
}
public function setCmsLanguageMlt(\Doctrine\Common\Collections\Collection $cmsLanguageMlt): self
{
    $this->cmsLanguageMlt = $cmsLanguageMlt;

    return $this;
}


  
    // TCMSFieldBoolean
public function isUseGooglesitemap(): bool
{
    return $this->useGooglesitemap;
}
public function setUseGooglesitemap(bool $useGooglesitemap): self
{
    $this->useGooglesitemap = $useGooglesitemap;

    return $this;
}


  
    // TCMSFieldVarchar
public function getMetaDescription(): string
{
    return $this->metaDescription;
}
public function setMetaDescription(string $metaDescription): self
{
    $this->metaDescription = $metaDescription;

    return $this;
}


  
    // TCMSFieldText
public function getMetaKeywords(): string
{
    return $this->metaKeywords;
}
public function setMetaKeywords(string $metaKeywords): self
{
    $this->metaKeywords = $metaKeywords;

    return $this;
}


  
    // TCMSFieldVarchar
public function getMetaAuthor(): string
{
    return $this->metaAuthor;
}
public function setMetaAuthor(string $metaAuthor): self
{
    $this->metaAuthor = $metaAuthor;

    return $this;
}


  
    // TCMSFieldVarchar
public function getMetaPublisher(): string
{
    return $this->metaPublisher;
}
public function setMetaPublisher(string $metaPublisher): self
{
    $this->metaPublisher = $metaPublisher;

    return $this;
}


  
    // TCMSFieldLookup
public function getCmsLocals(): \ChameleonSystem\CoreBundle\Entity\CmsLocals|null
{
    return $this->cmsLocals;
}
public function setCmsLocals(\ChameleonSystem\CoreBundle\Entity\CmsLocals|null $cmsLocals): self
{
    $this->cmsLocals = $cmsLocals;
    $this->cmsLocalsId = $cmsLocals?->getId();

    return $this;
}
public function getCmsLocalsId(): ?string
{
    return $this->cmsLocalsId;
}
public function setCmsLocalsId(?string $cmsLocalsId): self
{
    $this->cmsLocalsId = $cmsLocalsId;
    // todo - load new id
    //$this->cmsLocalsId = $?->getId();

    return $this;
}



  
    // TCMSFieldText
public function getCustomMetadata(): string
{
    return $this->customMetadata;
}
public function setCustomMetadata(string $customMetadata): self
{
    $this->customMetadata = $customMetadata;

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



  
    // TCMSFieldText
public function getActionPluginList(): string
{
    return $this->actionPluginList;
}
public function setActionPluginList(string $actionPluginList): self
{
    $this->actionPluginList = $actionPluginList;

    return $this;
}


  
    // TCMSFieldVarchar
public function getGoogleAnalyticNumber(): string
{
    return $this->googleAnalyticNumber;
}
public function setGoogleAnalyticNumber(string $googleAnalyticNumber): self
{
    $this->googleAnalyticNumber = $googleAnalyticNumber;

    return $this;
}


  
    // TCMSFieldVarchar
public function getEtrackerId(): string
{
    return $this->etrackerId;
}
public function setEtrackerId(string $etrackerId): self
{
    $this->etrackerId = $etrackerId;

    return $this;
}


  
    // TCMSFieldVarchar
public function getIvwId(): string
{
    return $this->ivwId;
}
public function setIvwId(string $ivwId): self
{
    $this->ivwId = $ivwId;

    return $this;
}


  
    // TCMSFieldBoolean
public function isIndexSearch(): bool
{
    return $this->indexSearch;
}
public function setIndexSearch(bool $indexSearch): self
{
    $this->indexSearch = $indexSearch;

    return $this;
}


  
    // TCMSFieldBoolean
public function isUseSlashInSeoUrls(): bool
{
    return $this->useSlashInSeoUrls;
}
public function setUseSlashInSeoUrls(bool $useSlashInSeoUrls): self
{
    $this->useSlashInSeoUrls = $useSlashInSeoUrls;

    return $this;
}


  
    // TCMSFieldBoolean
public function isDeactivePortal(): bool
{
    return $this->deactivePortal;
}
public function setDeactivePortal(bool $deactivePortal): self
{
    $this->deactivePortal = $deactivePortal;

    return $this;
}


  
    // TCMSFieldVarchar
public function getWysiwygCssUrl(): string
{
    return $this->wysiwygCssUrl;
}
public function setWysiwygCssUrl(string $wysiwygCssUrl): self
{
    $this->wysiwygCssUrl = $wysiwygCssUrl;

    return $this;
}


  
    // TCMSFieldPropertyTable
public function getCmsUrlAliasCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->cmsUrlAliasCollection;
}
public function setCmsUrlAliasCollection(\Doctrine\Common\Collections\Collection $cmsUrlAliasCollection): self
{
    $this->cmsUrlAliasCollection = $cmsUrlAliasCollection;

    return $this;
}


  
    // TCMSFieldText
public function getRobots(): string
{
    return $this->robots;
}
public function setRobots(string $robots): self
{
    $this->robots = $robots;

    return $this;
}


  
}
