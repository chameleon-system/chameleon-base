<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\ShopVariantType;
use ChameleonSystem\CoreBundle\Entity\CmsMedia;

class ShopVariantTypeValue {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldLookup
/** @var ShopVariantType|null - Belongs to variant type */
private ?ShopVariantType $shopVariantType = null
, 
    // TCMSFieldVarchar
/** @var string - Name */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - URL name (for article link) */
private string $urlName = '', 
    // TCMSFieldLookup
/** @var CmsMedia|null - Optional image or icon */
private ?CmsMedia $cmsMedia = null
, 
    // TCMSFieldVarchar
/** @var string - Alternative name (grouping) */
private string $nameGrouped = ''  ) {}

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
public function getShopVariantType(): ?ShopVariantType
{
    return $this->shopVariantType;
}

public function setShopVariantType(?ShopVariantType $shopVariantType): self
{
    $this->shopVariantType = $shopVariantType;

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
public function getUrlName(): string
{
    return $this->urlName;
}
public function setUrlName(string $urlName): self
{
    $this->urlName = $urlName;

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


  
}
