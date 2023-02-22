<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\PkgImageHotspotItem;
use ChameleonSystem\CoreBundle\Entity\ShopArticle;

class PkgImageHotspotItemSpot {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldLookup
/** @var PkgImageHotspotItem|null - Belongs to hotspot image */
private ?PkgImageHotspotItem $pkgImageHotspotItem = null
, 
    // TCMSFieldVarchar
/** @var string - Distance top */
private string $top = '', 
    // TCMSFieldVarchar
/** @var string - Distance left */
private string $left = '', 
    // TCMSFieldLookup
/** @var ShopArticle|null - Linked CMS object */
private ?ShopArticle $linkedRec = null
, 
    // TCMSFieldVarchar
/** @var string - External URL */
private string $externalUrl = ''  ) {}

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


  
    // TCMSFieldLookup
public function getLinkedRec(): ?ShopArticle
{
    return $this->linkedRec;
}

public function setLinkedRec(?ShopArticle $linkedRec): self
{
    $this->linkedRec = $linkedRec;

    return $this;
}


  
    // TCMSFieldVarchar
public function getExternalUrl(): string
{
    return $this->externalUrl;
}
public function setExternalUrl(string $externalUrl): self
{
    $this->externalUrl = $externalUrl;

    return $this;
}


  
}
