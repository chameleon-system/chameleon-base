<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgImageHotspotItemSpot {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\PkgImageHotspotItem|null - Belongs to hotspot image */
private \ChameleonSystem\CoreBundle\Entity\PkgImageHotspotItem|null $pkgImageHotspotItem = null,
/** @var null|string - Belongs to hotspot image */
private ?string $pkgImageHotspotItemId = null
, 
    // TCMSFieldNumber
/** @var int - Distance top */
private int $top = 0, 
    // TCMSFieldNumber
/** @var int - Distance left */
private int $left = 0, 
    // TCMSFieldOption
/** @var string - Hotspot icon type */
private string $hotspotType = 'Hotspot-Rechts', 
    // TCMSFieldExtendedLookupMultiTable
/** @var \ChameleonSystem\CoreBundle\Entity\ShopArticle|\ChameleonSystem\CoreBundle\Entity\ShopCategory|\ChameleonSystem\CoreBundle\Entity\CmsTplPage|null - Linked CMS object */
private \ChameleonSystem\CoreBundle\Entity\ShopArticle|\ChameleonSystem\CoreBundle\Entity\ShopCategory|\ChameleonSystem\CoreBundle\Entity\CmsTplPage|null $linkedRecord = null,
// TCMSFieldExtendedLookupMultiTable
/** @var string - Linked CMS object */
private string $linkedRecordTable = '', 
    // TCMSFieldURL
/** @var string - External URL */
private string $externalUrl = '', 
    // TCMSFieldText
/** @var string - Polygon area */
private string $polygonArea = '', 
    // TCMSFieldBoolean
/** @var bool - Show product info layover */
private bool $showSpot = true  ) {}

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


  
    // TCMSFieldOption
public function getHotspotType(): string
{
    return $this->hotspotType;
}
public function setHotspotType(string $hotspotType): self
{
    $this->hotspotType = $hotspotType;

    return $this;
}


  
    // TCMSFieldExtendedLookupMultiTable
public function getLinkedRecord(): \ChameleonSystem\CoreBundle\Entity\ShopArticle|\ChameleonSystem\CoreBundle\Entity\ShopCategory|\ChameleonSystem\CoreBundle\Entity\CmsTplPage|null
{
    return $this->linkedRecord;
}
public function setLinkedRecord(\ChameleonSystem\CoreBundle\Entity\ShopArticle|\ChameleonSystem\CoreBundle\Entity\ShopCategory|\ChameleonSystem\CoreBundle\Entity\CmsTplPage|null $linkedRecord): self
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
public function getExternalUrl(): string
{
    return $this->externalUrl;
}
public function setExternalUrl(string $externalUrl): self
{
    $this->externalUrl = $externalUrl;

    return $this;
}


  
    // TCMSFieldText
public function getPolygonArea(): string
{
    return $this->polygonArea;
}
public function setPolygonArea(string $polygonArea): self
{
    $this->polygonArea = $polygonArea;

    return $this;
}


  
    // TCMSFieldBoolean
public function isShowSpot(): bool
{
    return $this->showSpot;
}
public function setShowSpot(bool $showSpot): self
{
    $this->showSpot = $showSpot;

    return $this;
}


  
}
