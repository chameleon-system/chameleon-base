<?php

namespace ChameleonSystem\DataAccessBundle\Entity\CorePortal;

use ChameleonSystem\DataAccessBundle\Entity\Core\CmsLanguage;
use ChameleonSystem\DataAccessBundle\Entity\Core\CmsLocals;
use ChameleonSystem\DataAccessBundle\Entity\Core\CmsMessageManagerMessage;
use ChameleonSystem\DataAccessBundle\Entity\Core\CmsUrlAlias;
use ChameleonSystem\DataAccessBundle\Entity\Core\PkgCmsTheme;
use ChameleonSystem\DataAccessBundle\Entity\CoreMedia\CmsMedia;
use ChameleonSystem\DataAccessBundle\Entity\CorePagedef\CmsTree;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class CmsPortal
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

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
        // TCMSFieldLookup
        /** @var CmsLanguage|null - Portal language */
        private ?CmsLanguage $cmsLanguage = null,
        // TCMSFieldBoolean
        /** @var bool - Enable multi-language ability */
        private bool $useMultilanguage = false,
        // TCMSFieldBoolean
        /** @var bool - Show untranslated links */
        private bool $showNotTanslated = false,
        // ChameleonSystem\CoreBundle\Field\FieldTreeNodePortalSelect
        /** @var CmsTree|null - Navigation start node */
        private ?CmsTree $mainNodeTree = null,
        // TCMSFieldPropertyTable
        /** @var Collection<int, CmsPortalNavigation> - Navigations */
        private Collection $propertyNavigationsCollection = new ArrayCollection(),
        // TCMSFieldPortalHomeTreeNode
        /** @var CmsTree|null - Portal home page */
        private ?CmsTree $homeNode = null,
        // TCMSFieldPortalHomeTreeNode
        /** @var CmsTree|null - Page not found */
        private ?CmsTree $pageNotFoundNode = null,
        // TCMSFieldPropertyTable
        /** @var Collection<int, CmsDivision> - Sections */
        private Collection $cmsPortalDivisionsCollection = new ArrayCollection(),
        // TCMSFieldPosition
        /** @var int - Sorting */
        private int $sortOrder = 0,
        // TCMSFieldPropertyTable
        /** @var Collection<int, CmsPortalDomains> - Domains */
        private Collection $cmsPortalDomainsCollection = new ArrayCollection(),
        // TCMSFieldURL
        /** @var string - Favicon URL */
        private string $faviconUrl = '/favicon.ico',
        // TCMSFieldExtendedLookupMedia
        /** @var CmsMedia|null - Logo */
        private ?CmsMedia $images = null,
        // TCMSFieldExtendedLookupMedia
        /** @var CmsMedia|null - Logo for watermarking */
        private ?CmsMedia $watermarkLogo = null,
        // TCMSFieldExtendedLookupMedia
        /** @var CmsMedia|null - Background image */
        private ?CmsMedia $backgroundImage = null,
        // TCMSFieldPropertyTable
        /** @var Collection<int, CmsMessageManagerMessage> - System messages / error codes */
        private Collection $cmsMessageManagerMessageCollection = new ArrayCollection(),
        // TCMSFieldPropertyTable
        /** @var Collection<int, CmsPortalSystemPage> - System pages */
        private Collection $cmsPortalSystemPageCollection = new ArrayCollection(),
        // TCMSFieldLookupMultiselectCheckboxesPossibleLanguages
        /** @var Collection<int, CmsLanguage> - Portal languages */
        private Collection $cmsLanguageCollection = new ArrayCollection(),
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
        // TCMSFieldLookup
        /** @var CmsLocals|null - Locale */
        private ?CmsLocals $cmsLocals = null,
        // TCMSFieldText
        /** @var string - Your meta data */
        private string $customMetadata = '',
        // TCMSFieldExtendedLookup
        /** @var PkgCmsTheme|null - Website presentation / theme */
        private ?PkgCmsTheme $pkgCmsTheme = null,
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
        /** @var Collection<int, CmsUrlAlias> - URL alias list */
        private Collection $cmsUrlAliasCollection = new ArrayCollection(),
        // TCMSFieldText
        /** @var string - robots.txt */
        private string $robots = ''
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
    public function getCmsLanguage(): ?CmsLanguage
    {
        return $this->cmsLanguage;
    }

    public function setCmsLanguage(?CmsLanguage $cmsLanguage): self
    {
        $this->cmsLanguage = $cmsLanguage;

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
    public function getMainNodeTree(): ?CmsTree
    {
        return $this->mainNodeTree;
    }

    public function setMainNodeTree(?CmsTree $mainNodeTree): self
    {
        $this->mainNodeTree = $mainNodeTree;

        return $this;
    }

    // TCMSFieldPropertyTable

    /**
     * @return Collection<int, CmsPortalNavigation>
     */
    public function getPropertyNavigationsCollection(): Collection
    {
        return $this->propertyNavigationsCollection;
    }

    public function addPropertyNavigationsCollection(CmsPortalNavigation $propertyNavigations): self
    {
        if (!$this->propertyNavigationsCollection->contains($propertyNavigations)) {
            $this->propertyNavigationsCollection->add($propertyNavigations);
            $propertyNavigations->setCmsPortal($this);
        }

        return $this;
    }

    public function removePropertyNavigationsCollection(CmsPortalNavigation $propertyNavigations): self
    {
        if ($this->propertyNavigationsCollection->removeElement($propertyNavigations)) {
            // set the owning side to null (unless already changed)
            if ($propertyNavigations->getCmsPortal() === $this) {
                $propertyNavigations->setCmsPortal(null);
            }
        }

        return $this;
    }

    // TCMSFieldPortalHomeTreeNode
    public function getHomeNode(): ?CmsTree
    {
        return $this->homeNode;
    }

    public function setHomeNode(?CmsTree $homeNode): self
    {
        $this->homeNode = $homeNode;

        return $this;
    }

    // TCMSFieldPortalHomeTreeNode
    public function getPageNotFoundNode(): ?CmsTree
    {
        return $this->pageNotFoundNode;
    }

    public function setPageNotFoundNode(?CmsTree $pageNotFoundNode): self
    {
        $this->pageNotFoundNode = $pageNotFoundNode;

        return $this;
    }

    // TCMSFieldPropertyTable

    /**
     * @return Collection<int, CmsDivision>
     */
    public function getCmsPortalDivisionsCollection(): Collection
    {
        return $this->cmsPortalDivisionsCollection;
    }

    public function addCmsPortalDivisionsCollection(CmsDivision $cmsPortalDivisions): self
    {
        if (!$this->cmsPortalDivisionsCollection->contains($cmsPortalDivisions)) {
            $this->cmsPortalDivisionsCollection->add($cmsPortalDivisions);
            $cmsPortalDivisions->setCmsPortal($this);
        }

        return $this;
    }

    public function removeCmsPortalDivisionsCollection(CmsDivision $cmsPortalDivisions): self
    {
        if ($this->cmsPortalDivisionsCollection->removeElement($cmsPortalDivisions)) {
            // set the owning side to null (unless already changed)
            if ($cmsPortalDivisions->getCmsPortal() === $this) {
                $cmsPortalDivisions->setCmsPortal(null);
            }
        }

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

    /**
     * @return Collection<int, CmsPortalDomains>
     */
    public function getCmsPortalDomainsCollection(): Collection
    {
        return $this->cmsPortalDomainsCollection;
    }

    public function addCmsPortalDomainsCollection(CmsPortalDomains $cmsPortalDomains): self
    {
        if (!$this->cmsPortalDomainsCollection->contains($cmsPortalDomains)) {
            $this->cmsPortalDomainsCollection->add($cmsPortalDomains);
            $cmsPortalDomains->setCmsPortal($this);
        }

        return $this;
    }

    public function removeCmsPortalDomainsCollection(CmsPortalDomains $cmsPortalDomains): self
    {
        if ($this->cmsPortalDomainsCollection->removeElement($cmsPortalDomains)) {
            // set the owning side to null (unless already changed)
            if ($cmsPortalDomains->getCmsPortal() === $this) {
                $cmsPortalDomains->setCmsPortal(null);
            }
        }

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

    // TCMSFieldExtendedLookupMedia
    public function getImages(): ?CmsMedia
    {
        return $this->images;
    }

    public function setImages(?CmsMedia $images): self
    {
        $this->images = $images;

        return $this;
    }

    // TCMSFieldExtendedLookupMedia
    public function getWatermarkLogo(): ?CmsMedia
    {
        return $this->watermarkLogo;
    }

    public function setWatermarkLogo(?CmsMedia $watermarkLogo): self
    {
        $this->watermarkLogo = $watermarkLogo;

        return $this;
    }

    // TCMSFieldExtendedLookupMedia
    public function getBackgroundImage(): ?CmsMedia
    {
        return $this->backgroundImage;
    }

    public function setBackgroundImage(?CmsMedia $backgroundImage): self
    {
        $this->backgroundImage = $backgroundImage;

        return $this;
    }

    // TCMSFieldPropertyTable

    /**
     * @return Collection<int, CmsMessageManagerMessage>
     */
    public function getCmsMessageManagerMessageCollection(): Collection
    {
        return $this->cmsMessageManagerMessageCollection;
    }

    public function addCmsMessageManagerMessageCollection(CmsMessageManagerMessage $cmsMessageManagerMessage): self
    {
        if (!$this->cmsMessageManagerMessageCollection->contains($cmsMessageManagerMessage)) {
            $this->cmsMessageManagerMessageCollection->add($cmsMessageManagerMessage);
            $cmsMessageManagerMessage->setCmsPortal($this);
        }

        return $this;
    }

    public function removeCmsMessageManagerMessageCollection(CmsMessageManagerMessage $cmsMessageManagerMessage): self
    {
        if ($this->cmsMessageManagerMessageCollection->removeElement($cmsMessageManagerMessage)) {
            // set the owning side to null (unless already changed)
            if ($cmsMessageManagerMessage->getCmsPortal() === $this) {
                $cmsMessageManagerMessage->setCmsPortal(null);
            }
        }

        return $this;
    }

    // TCMSFieldPropertyTable

    /**
     * @return Collection<int, CmsPortalSystemPage>
     */
    public function getCmsPortalSystemPageCollection(): Collection
    {
        return $this->cmsPortalSystemPageCollection;
    }

    public function addCmsPortalSystemPageCollection(CmsPortalSystemPage $cmsPortalSystemPage): self
    {
        if (!$this->cmsPortalSystemPageCollection->contains($cmsPortalSystemPage)) {
            $this->cmsPortalSystemPageCollection->add($cmsPortalSystemPage);
            $cmsPortalSystemPage->setCmsPortal($this);
        }

        return $this;
    }

    public function removeCmsPortalSystemPageCollection(CmsPortalSystemPage $cmsPortalSystemPage): self
    {
        if ($this->cmsPortalSystemPageCollection->removeElement($cmsPortalSystemPage)) {
            // set the owning side to null (unless already changed)
            if ($cmsPortalSystemPage->getCmsPortal() === $this) {
                $cmsPortalSystemPage->setCmsPortal(null);
            }
        }

        return $this;
    }

    // TCMSFieldLookupMultiselectCheckboxesPossibleLanguages

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
    public function getCmsLocals(): ?CmsLocals
    {
        return $this->cmsLocals;
    }

    public function setCmsLocals(?CmsLocals $cmsLocals): self
    {
        $this->cmsLocals = $cmsLocals;

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

    // TCMSFieldExtendedLookup
    public function getPkgCmsTheme(): ?PkgCmsTheme
    {
        return $this->pkgCmsTheme;
    }

    public function setPkgCmsTheme(?PkgCmsTheme $pkgCmsTheme): self
    {
        $this->pkgCmsTheme = $pkgCmsTheme;

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

    /**
     * @return Collection<int, CmsUrlAlias>
     */
    public function getCmsUrlAliasCollection(): Collection
    {
        return $this->cmsUrlAliasCollection;
    }

    public function addCmsUrlAliasCollection(CmsUrlAlias $cmsUrlAlias): self
    {
        if (!$this->cmsUrlAliasCollection->contains($cmsUrlAlias)) {
            $this->cmsUrlAliasCollection->add($cmsUrlAlias);
            $cmsUrlAlias->setCmsPortal($this);
        }

        return $this;
    }

    public function removeCmsUrlAliasCollection(CmsUrlAlias $cmsUrlAlias): self
    {
        if ($this->cmsUrlAliasCollection->removeElement($cmsUrlAlias)) {
            // set the owning side to null (unless already changed)
            if ($cmsUrlAlias->getCmsPortal() === $this) {
                $cmsUrlAlias->setCmsPortal(null);
            }
        }

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
