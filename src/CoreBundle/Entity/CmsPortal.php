<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\CmsLanguage;
use ChameleonSystem\CoreBundle\Entity\CmsPortalNavigation;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use ChameleonSystem\CoreBundle\Entity\CmsDivision;
use ChameleonSystem\CoreBundle\Entity\CmsPortalDomains;
use ChameleonSystem\CoreBundle\Entity\CmsMedia;
use ChameleonSystem\CoreBundle\Entity\CmsMessageManagerMessage;
use ChameleonSystem\CoreBundle\Entity\CmsPortalSystemPage;
use ChameleonSystem\CoreBundle\Entity\CmsLocals;
use ChameleonSystem\CoreBundle\Entity\PkgCmsTheme;
use ChameleonSystem\CoreBundle\Entity\CmsUrlAlias;

class CmsPortal {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
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
private ?CmsLanguage $cmsLanguage = null
, 
    // TCMSFieldPropertyTable
/** @var Collection<int, CmsPortalNavigation> - Navigations */
private Collection $propertyNavigationsCollection = new ArrayCollection()
, 
    // TCMSFieldPropertyTable
/** @var Collection<int, CmsDivision> - Sections */
private Collection $cmsPortalDivisionsCollection = new ArrayCollection()
, 
    // TCMSFieldPropertyTable
/** @var Collection<int, CmsPortalDomains> - Domains */
private Collection $cmsPortalDomainsCollection = new ArrayCollection()
, 
    // TCMSFieldVarchar
/** @var string - Favicon URL */
private string $faviconUrl = '/favicon.ico', 
    // TCMSFieldLookup
/** @var CmsMedia|null - Logo */
private ?CmsMedia $ima = null
, 
    // TCMSFieldLookup
/** @var CmsMedia|null - Logo for watermarking */
private ?CmsMedia $watermarkL = null
, 
    // TCMSFieldLookup
/** @var CmsMedia|null - Background image */
private ?CmsMedia $backgroundIm = null
, 
    // TCMSFieldPropertyTable
/** @var Collection<int, CmsMessageManagerMessage> - System messages / error codes */
private Collection $cmsMessageManagerMessageCollection = new ArrayCollection()
, 
    // TCMSFieldPropertyTable
/** @var Collection<int, CmsPortalSystemPage> - System pages */
private Collection $cmsPortalSystemPageCollection = new ArrayCollection()
, 
    // TCMSFieldVarchar
/** @var string - Short description */
private string $metaDescription = '', 
    // TCMSFieldVarchar
/** @var string - Author */
private string $metaAuthor = '', 
    // TCMSFieldVarchar
/** @var string - Publisher */
private string $metaPublisher = '', 
    // TCMSFieldLookup
/** @var CmsLocals|null - Locale */
private ?CmsLocals $cmsLocals = null
, 
    // TCMSFieldLookup
/** @var PkgCmsTheme|null - Website presentation / theme */
private ?PkgCmsTheme $pkgCmsTheme = null
, 
    // TCMSFieldVarchar
/** @var string - Google Analytics ID */
private string $googleAnalyticNumber = '', 
    // TCMSFieldVarchar
/** @var string - etracker ID */
private string $etrackerId = '', 
    // TCMSFieldVarchar
/** @var string - IVW ID */
private string $ivwId = '', 
    // TCMSFieldVarchar
/** @var string - WYSIWYG text editor CSS URL */
private string $wysiwygCssUrl = '', 
    // TCMSFieldPropertyTable
/** @var Collection<int, CmsUrlAlias> - URL alias list */
private Collection $cmsUrlAliasCollection = new ArrayCollection()
  ) {}

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


  
    // TCMSFieldVarchar
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
public function getIma(): ?CmsMedia
{
    return $this->ima;
}

public function setIma(?CmsMedia $ima): self
{
    $this->ima = $ima;

    return $this;
}


  
    // TCMSFieldLookup
public function getWatermarkL(): ?CmsMedia
{
    return $this->watermarkL;
}

public function setWatermarkL(?CmsMedia $watermarkL): self
{
    $this->watermarkL = $watermarkL;

    return $this;
}


  
    // TCMSFieldLookup
public function getBackgroundIm(): ?CmsMedia
{
    return $this->backgroundIm;
}

public function setBackgroundIm(?CmsMedia $backgroundIm): self
{
    $this->backgroundIm = $backgroundIm;

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


  
}
