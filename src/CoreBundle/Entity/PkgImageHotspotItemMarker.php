<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\PkgImageHotspotItem;
use ChameleonSystem\CoreBundle\Entity\CmsTplPage;
use ChameleonSystem\CoreBundle\Entity\ShopCategory;
use ChameleonSystem\CoreBundle\Entity\ShopArticle;
use ChameleonSystem\CoreBundle\Entity\CmsMedia;

class PkgImageHotspotItemMarker {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldLookup
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
    // TCMSFieldExtendedLookupMultiTable
/** @var CmsTplPage|ShopCategory|ShopArticle|null - Link to object */
private ?CmsTplPage|ShopCategory|ShopArticle $linkedRec = null
,
// TCMSFieldExtendedLookupMultiTable
/** @var string - Link to object */
private string $linkedRecordTableName = '', 
    // TCMSFieldVarchar
/** @var string - Alternative link */
private string $url = '', 
    // TCMSFieldLookup
/** @var CmsMedia|null - Image */
private ?CmsMedia $cmsMedia = null
, 
    // TCMSFieldLookup
/** @var CmsMedia|null - Hover image */
private ?CmsMedia $cmsMediaHover = null
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


  
    // TCMSFieldExtendedLookupMultiTable
public function getLinkedRec(): ?CmsTplPage|ShopCategory|ShopArticle
{
    return $this->linkedRec;
}

public function setLinkedRec(?CmsTplPage|ShopCategory|ShopArticle $linkedRec): self
{
    $this->linkedRec = $linkedRec;

    return $this;
}
// TCMSFieldExtendedLookupMultiTable
public function getLinkedRecord(): string
{
    return $this->linkedRecordTableName;
}
public function setLinkedRecord(string $linkedRecordTableName): self
{
    $this->linkedRecordTableName = $linkedRecordTableName;

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


  
    // TCMSFieldLookup
public function getCmsMediaHover(): ?CmsMedia
{
    return $this->cmsMediaHover;
}

public function setCmsMediaHover(?CmsMedia $cmsMediaHover): self
{
    $this->cmsMediaHover = $cmsMediaHover;

    return $this;
}


  
}
