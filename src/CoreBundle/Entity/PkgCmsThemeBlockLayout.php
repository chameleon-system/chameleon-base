<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgCmsThemeBlockLayout {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\PkgCmsThemeBlock|null - Belongs to theme block */
private \ChameleonSystem\CoreBundle\Entity\PkgCmsThemeBlock|null $pkgCmsThemeBlock = null,
/** @var null|string - Belongs to theme block */
private ?string $pkgCmsThemeBlockId = null
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
/** @var string - Layout file (path) */
private string $layoutFile = '', 
    // TCMSFieldVarchar
/** @var string - Path to own LESS/CSS */
private string $lessFile = ''  ) {}

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
    // TCMSFieldLookup
public function getPkgCmsThemeBlock(): \ChameleonSystem\CoreBundle\Entity\PkgCmsThemeBlock|null
{
    return $this->pkgCmsThemeBlock;
}
public function setPkgCmsThemeBlock(\ChameleonSystem\CoreBundle\Entity\PkgCmsThemeBlock|null $pkgCmsThemeBlock): self
{
    $this->pkgCmsThemeBlock = $pkgCmsThemeBlock;
    $this->pkgCmsThemeBlockId = $pkgCmsThemeBlock?->getId();

    return $this;
}
public function getPkgCmsThemeBlockId(): ?string
{
    return $this->pkgCmsThemeBlockId;
}
public function setPkgCmsThemeBlockId(?string $pkgCmsThemeBlockId): self
{
    $this->pkgCmsThemeBlockId = $pkgCmsThemeBlockId;
    // todo - load new id
    //$this->pkgCmsThemeBlockId = $?->getId();

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
public function getLayoutFile(): string
{
    return $this->layoutFile;
}
public function setLayoutFile(string $layoutFile): self
{
    $this->layoutFile = $layoutFile;

    return $this;
}


  
    // TCMSFieldVarchar
public function getLessFile(): string
{
    return $this->lessFile;
}
public function setLessFile(string $lessFile): self
{
    $this->lessFile = $lessFile;

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
