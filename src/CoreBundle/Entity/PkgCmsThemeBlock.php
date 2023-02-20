<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgCmsThemeBlock {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\PkgCmsThemeBlockLayout|null - Default layout */
private \ChameleonSystem\CoreBundle\Entity\PkgCmsThemeBlockLayout|null $pkgCmsThemeBlockLayout = null,
/** @var null|string - Default layout */
private ?string $pkgCmsThemeBlockLayoutId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsMedia|null - Preview image */
private \ChameleonSystem\CoreBundle\Entity\CmsMedia|null $cmsMedia = null,
/** @var null|string - Preview image */
private ?string $cmsMediaId = null
, 
    // TCMSFieldVarchar
/** @var string - Descriptive name */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - System name */
private string $systemName = '', 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\CmsMasterPagedefSpot[] - Spots */
private \Doctrine\Common\Collections\Collection $cmsMasterPagedefSpotCollection = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\PkgCmsThemeBlockLayout[] - Layouts */
private \Doctrine\Common\Collections\Collection $pkgCmsThemeBlockLayoutCollection = new \Doctrine\Common\Collections\ArrayCollection()  ) {}

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
public function getSystemName(): string
{
    return $this->systemName;
}
public function setSystemName(string $systemName): self
{
    $this->systemName = $systemName;

    return $this;
}


  
    // TCMSFieldPropertyTable
public function getCmsMasterPagedefSpotCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->cmsMasterPagedefSpotCollection;
}
public function setCmsMasterPagedefSpotCollection(\Doctrine\Common\Collections\Collection $cmsMasterPagedefSpotCollection): self
{
    $this->cmsMasterPagedefSpotCollection = $cmsMasterPagedefSpotCollection;

    return $this;
}


  
    // TCMSFieldPropertyTable
public function getPkgCmsThemeBlockLayoutCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->pkgCmsThemeBlockLayoutCollection;
}
public function setPkgCmsThemeBlockLayoutCollection(\Doctrine\Common\Collections\Collection $pkgCmsThemeBlockLayoutCollection): self
{
    $this->pkgCmsThemeBlockLayoutCollection = $pkgCmsThemeBlockLayoutCollection;

    return $this;
}


  
    // TCMSFieldLookup
public function getPkgCmsThemeBlockLayout(): \ChameleonSystem\CoreBundle\Entity\PkgCmsThemeBlockLayout|null
{
    return $this->pkgCmsThemeBlockLayout;
}
public function setPkgCmsThemeBlockLayout(\ChameleonSystem\CoreBundle\Entity\PkgCmsThemeBlockLayout|null $pkgCmsThemeBlockLayout): self
{
    $this->pkgCmsThemeBlockLayout = $pkgCmsThemeBlockLayout;
    $this->pkgCmsThemeBlockLayoutId = $pkgCmsThemeBlockLayout?->getId();

    return $this;
}
public function getPkgCmsThemeBlockLayoutId(): ?string
{
    return $this->pkgCmsThemeBlockLayoutId;
}
public function setPkgCmsThemeBlockLayoutId(?string $pkgCmsThemeBlockLayoutId): self
{
    $this->pkgCmsThemeBlockLayoutId = $pkgCmsThemeBlockLayoutId;
    // todo - load new id
    //$this->pkgCmsThemeBlockLayoutId = $?->getId();

    return $this;
}



  
    // TCMSFieldLookup
public function getCmsMedia(): \ChameleonSystem\CoreBundle\Entity\CmsMedia|null
{
    return $this->cmsMedia;
}
public function setCmsMedia(\ChameleonSystem\CoreBundle\Entity\CmsMedia|null $cmsMedia): self
{
    $this->cmsMedia = $cmsMedia;
    $this->cmsMediaId = $cmsMedia?->getId();

    return $this;
}
public function getCmsMediaId(): ?string
{
    return $this->cmsMediaId;
}
public function setCmsMediaId(?string $cmsMediaId): self
{
    $this->cmsMediaId = $cmsMediaId;
    // todo - load new id
    //$this->cmsMediaId = $?->getId();

    return $this;
}



  
}
