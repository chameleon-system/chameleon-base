<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\PkgImageHotspotItem;

class PkgImageHotspotItemMarker {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldLookupParentID
/** @var PkgImageHotspotItem|null - Belongs to hotspot image */
private ?PkgImageHotspotItem $pkgImageHotspotItem = null
, 
    // TCMSFieldVarchar
/** @var string - Alt or link text of the image */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - Position of top border relative to top border of background image */
private string $top = '', 
    // TCMSFieldVarchar
/** @var string - Position of left border relative to left border of background image */
private string $left = '', 
    // TCMSFieldVarchar
/** @var string - Alternative link */
private string $url = ''  ) {}

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
    // TCMSFieldLookupParentID
public function getPkgImageHotspotItem(): ?PkgImageHotspotItem
{
    return $this->pkgImageHotspotItem;
}

public function setPkgImageHotspotItem(?PkgImageHotspotItem $pkgImageHotspotItem): self
{
    $this->pkgImageHotspotItem = $pkgImageHotspotItem;

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
public function getTop(): string
{
    return $this->top;
}
public function setTop(string $top): self
{
    $this->top = $top;

    return $this;
}


  
    // TCMSFieldVarchar
public function getLeft(): string
{
    return $this->left;
}
public function setLeft(string $left): self
{
    $this->left = $left;

    return $this;
}


  
    // TCMSFieldVarchar
public function getUrl(): string
{
    return $this->url;
}
public function setUrl(string $url): self
{
    $this->url = $url;

    return $this;
}


  
}
