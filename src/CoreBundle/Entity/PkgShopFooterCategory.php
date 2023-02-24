<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\ShopCategory;
use ChameleonSystem\CoreBundle\Entity\Shop;

class PkgShopFooterCategory {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - Main category / heading */
private string $name = '', 
    // TCMSFieldLookup
/** @var ShopCategory|null - Product category */
private ?ShopCategory $shopCategory = null
, 
    // TCMSFieldLookup
/** @var Shop|null - Belongs to shop */
private ?Shop $shop = null
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
public function getShopCategory(): ?ShopCategory
{
    return $this->shopCategory;
}

public function setShopCategory(?ShopCategory $shopCategory): self
{
    $this->shopCategory = $shopCategory;

    return $this;
}


  
    // TCMSFieldLookup
public function getShop(): ?Shop
{
    return $this->shop;
}

public function setShop(?Shop $shop): self
{
    $this->shop = $shop;

    return $this;
}


  
}