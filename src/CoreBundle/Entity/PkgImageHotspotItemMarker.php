<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgImageHotspotItemMarker {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\PkgImageHotspotItem|null - Belongs to hotspot image */
private \ChameleonSystem\CoreBundle\Entity\PkgImageHotspotItem|null $pkgImageHotspotItem = null,
/** @var null|string - Belongs to hotspot image */
private ?string $pkgImageHotspotItemId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsMedia|null - Image */
private \ChameleonSystem\CoreBundle\Entity\CmsMedia|null $cmsMedia = null,
/** @var null|string - Image */
private ?string $cmsMediaId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsMedia|null - Hover image */
private \ChameleonSystem\CoreBundle\Entity\CmsMedia|null $cmsMediaHover = null,
/** @var null|string - Hover image */
private ?string $cmsMediaHoverId = null
, 
    // TCMSFieldVarchar
/** @var string - Alt or link text of the image */
private string $name = '', 
    // TCMSFieldNumber
/** @var int - Position of top border relative to top border of background image */
private int $top = 0, 
    // TCMSFieldNumber
/** @var int - Position of left border relative to left border of background image */
private int $left = 0, 
    // TCMSFieldExtendedLookupMultiTable
/** @var \ChameleonSystem\CoreBundle\Entity\CmsTplPage|\ChameleonSystem\CoreBundle\Entity\ShopCategory|\ChameleonSystem\CoreBundle\Entity\ShopArticle|null - Link to object */
private \ChameleonSystem\CoreBundle\Entity\CmsTplPage|\ChameleonSystem\CoreBundle\Entity\ShopCategory|\ChameleonSystem\CoreBundle\Entity\ShopArticle|null $linkedRecord = null,
// TCMSFieldExtendedLookupMultiTable
/** @var string - Link to object */
private string $linkedRecordTable = '', 
    // TCMSFieldURL
/** @var string - Alternative link */
private string $url = '', 
    // TCMSFieldBoolean
/** @var bool - Show object layover */
private bool $showObjectLayover = false  ) {}

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
public function getPkgImageHotspotItem(): \ChameleonSystem\CoreBundle\Entity\PkgImageHotspotItem|null
{
    return $this->pkgImageHotspotItem;
}
public function setPkgImageHotspotItem(\ChameleonSystem\CoreBundle\Entity\PkgImageHotspotItem|null $pkgImageHotspotItem): self
{
    $this->pkgImageHotspotItem = $pkgImageHotspotItem;
    $this->pkgImageHotspotItemId = $pkgImageHotspotItem?->getId();

    return $this;
}
public function getPkgImageHotspotItemId(): ?string
{
    return $this->pkgImageHotspotItemId;
}
public function setPkgImageHotspotItemId(?string $pkgImageHotspotItemId): self
{
    $this->pkgImageHotspotItemId = $pkgImageHotspotItemId;
    // todo - load new id
    //$this->pkgImageHotspotItemId = $?->getId();

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


  
    // TCMSFieldNumber
public function getTop(): int
{
    return $this->top;
}
public function setTop(int $top): self
{
    $this->top = $top;

    return $this;
}


  
    // TCMSFieldNumber
public function getLeft(): int
{
    return $this->left;
}
public function setLeft(int $left): self
{
    $this->left = $left;

    return $this;
}


  
    // TCMSFieldExtendedLookupMultiTable
public function getLinkedRecord(): \ChameleonSystem\CoreBundle\Entity\CmsTplPage|\ChameleonSystem\CoreBundle\Entity\ShopCategory|\ChameleonSystem\CoreBundle\Entity\ShopArticle|null
{
    return $this->linkedRecord;
}
public function setLinkedRecord(\ChameleonSystem\CoreBundle\Entity\CmsTplPage|\ChameleonSystem\CoreBundle\Entity\ShopCategory|\ChameleonSystem\CoreBundle\Entity\ShopArticle|null $linkedRecord): self
{
    $this->linkedRecord = $linkedRecord;

    return $this;
}

// TCMSFieldExtendedLookupMultiTable
public function getLinkedRecordTable(): string
{
    return $this->linkedRecordTable;
}
public function setLinkedRecordTable(string $linkedRecordTable): self
{
    $this->linkedRecordTable = $linkedRecordTable;

    return $this;
}


  
    // TCMSFieldURL
public function getUrl(): string
{
    return $this->url;
}
public function setUrl(string $url): self
{
    $this->url = $url;

    return $this;
}


  
    // TCMSFieldBoolean
public function isShowObjectLayover(): bool
{
    return $this->showObjectLayover;
}
public function setShowObjectLayover(bool $showObjectLayover): self
{
    $this->showObjectLayover = $showObjectLayover;

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



  
    // TCMSFieldLookup
public function getCmsMediaHover(): \ChameleonSystem\CoreBundle\Entity\CmsMedia|null
{
    return $this->cmsMediaHover;
}
public function setCmsMediaHover(\ChameleonSystem\CoreBundle\Entity\CmsMedia|null $cmsMediaHover): self
{
    $this->cmsMediaHover = $cmsMediaHover;
    $this->cmsMediaHoverId = $cmsMediaHover?->getId();

    return $this;
}
public function getCmsMediaHoverId(): ?string
{
    return $this->cmsMediaHoverId;
}
public function setCmsMediaHoverId(?string $cmsMediaHoverId): self
{
    $this->cmsMediaHoverId = $cmsMediaHoverId;
    // todo - load new id
    //$this->cmsMediaHoverId = $?->getId();

    return $this;
}



  
}
