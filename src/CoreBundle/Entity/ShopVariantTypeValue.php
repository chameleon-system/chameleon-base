<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopVariantTypeValue {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\ShopVariantType|null - Belongs to variant type */
private \ChameleonSystem\CoreBundle\Entity\ShopVariantType|null $shopVariantType = null,
/** @var null|string - Belongs to variant type */
private ?string $shopVariantTypeId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsMedia|null - Optional image or icon */
private \ChameleonSystem\CoreBundle\Entity\CmsMedia|null $cmsMedia = null,
/** @var null|string - Optional image or icon */
private ?string $cmsMediaId = null
, 
    // TCMSFieldVarchar
/** @var string - Name */
private string $name = '', 
    // TCMSFieldSEOURLTitle
/** @var string - URL name (for article link) */
private string $urlName = '', 
    // TCMSFieldPosition
/** @var int - Position */
private int $position = 0, 
    // TCMSFieldColorpicker
/** @var string - Color value (optional) */
private string $colorCode = '', 
    // TCMSFieldVarchar
/** @var string - Alternative name (grouping) */
private string $nameGrouped = '', 
    // TCMSFieldPrice
/** @var float - Surcharge / reduction */
private float $surcharge = 0  ) {}

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
public function getShopVariantType(): \ChameleonSystem\CoreBundle\Entity\ShopVariantType|null
{
    return $this->shopVariantType;
}
public function setShopVariantType(\ChameleonSystem\CoreBundle\Entity\ShopVariantType|null $shopVariantType): self
{
    $this->shopVariantType = $shopVariantType;
    $this->shopVariantTypeId = $shopVariantType?->getId();

    return $this;
}
public function getShopVariantTypeId(): ?string
{
    return $this->shopVariantTypeId;
}
public function setShopVariantTypeId(?string $shopVariantTypeId): self
{
    $this->shopVariantTypeId = $shopVariantTypeId;
    // todo - load new id
    //$this->shopVariantTypeId = $?->getId();

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


  
    // TCMSFieldSEOURLTitle
public function getUrlName(): string
{
    return $this->urlName;
}
public function setUrlName(string $urlName): self
{
    $this->urlName = $urlName;

    return $this;
}


  
    // TCMSFieldPosition
public function getPosition(): int
{
    return $this->position;
}
public function setPosition(int $position): self
{
    $this->position = $position;

    return $this;
}


  
    // TCMSFieldColorpicker
public function getColorCode(): string
{
    return $this->colorCode;
}
public function setColorCode(string $colorCode): self
{
    $this->colorCode = $colorCode;

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



  
    // TCMSFieldVarchar
public function getNameGrouped(): string
{
    return $this->nameGrouped;
}
public function setNameGrouped(string $nameGrouped): self
{
    $this->nameGrouped = $nameGrouped;

    return $this;
}


  
    // TCMSFieldPrice
public function getSurcharge(): float
{
    return $this->surcharge;
}
public function setSurcharge(float $surcharge): self
{
    $this->surcharge = $surcharge;

    return $this;
}


  
}
