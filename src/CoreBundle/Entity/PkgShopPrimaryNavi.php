<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\CmsPortal;
use ChameleonSystem\CoreBundle\Entity\CmsTree;

class PkgShopPrimaryNavi {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldLookup
/** @var CmsPortal|null - Belongs to portal */
private ?CmsPortal $cmsPortal = null
, 
    // TCMSFieldVarchar
/** @var string - Name */
private string $name = '', 
    // TCMSFieldLookup
/** @var CmsTree|null - Select navigation */
private ?CmsTree $tar = null
, 
    // TCMSFieldVarchar
/** @var string - Individual CSS class */
private string $cssClass = ''  ) {}

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
public function getCmsPortal(): ?CmsPortal
{
    return $this->cmsPortal;
}

public function setCmsPortal(?CmsPortal $cmsPortal): self
{
    $this->cmsPortal = $cmsPortal;

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


  
    // TCMSFieldLookup
public function getTar(): ?CmsTree
{
    return $this->tar;
}

public function setTar(?CmsTree $tar): self
{
    $this->tar = $tar;

    return $this;
}


  
    // TCMSFieldVarchar
public function getCssClass(): string
{
    return $this->cssClass;
}
public function setCssClass(string $cssClass): self
{
    $this->cssClass = $cssClass;

    return $this;
}


  
}
