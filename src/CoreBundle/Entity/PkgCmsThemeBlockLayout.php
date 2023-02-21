<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\PkgCmsThemeBlock;
use ChameleonSystem\CoreBundle\Entity\CmsMedia;

class PkgCmsThemeBlockLayout {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldLookup
/** @var PkgCmsThemeBlock|null - Belongs to theme block */
private ?PkgCmsThemeBlock $pkgCmsThemeBlock = null
, 
    // TCMSFieldVarchar
/** @var string - Descriptive name */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - Layout file (path) */
private string $layoutFile = '', 
    // TCMSFieldVarchar
/** @var string - Path to own LESS/CSS */
private string $lessFile = '', 
    // TCMSFieldLookup
/** @var CmsMedia|null - Preview image */
private ?CmsMedia $cmsMedia = null
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
    // TCMSFieldLookup
public function getPkgCmsThemeBlock(): ?PkgCmsThemeBlock
{
    return $this->pkgCmsThemeBlock;
}

public function setPkgCmsThemeBlock(?PkgCmsThemeBlock $pkgCmsThemeBlock): self
{
    $this->pkgCmsThemeBlock = $pkgCmsThemeBlock;

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
public function getCmsMedia(): ?CmsMedia
{
    return $this->cmsMedia;
}

public function setCmsMedia(?CmsMedia $cmsMedia): self
{
    $this->cmsMedia = $cmsMedia;

    return $this;
}


  
}
