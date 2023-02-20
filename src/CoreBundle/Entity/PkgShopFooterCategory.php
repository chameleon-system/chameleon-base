<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgShopFooterCategory {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\ShopCategory|null - Product category */
private \ChameleonSystem\CoreBundle\Entity\ShopCategory|null $shopCategory = null,
/** @var null|string - Product category */
private ?string $shopCategoryId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\Shop|null - Belongs to shop */
private \ChameleonSystem\CoreBundle\Entity\Shop|null $shop = null,
/** @var null|string - Belongs to shop */
private ?string $shopId = null
, 
    // TCMSFieldVarchar
/** @var string - Main category / heading */
private string $name = '', 
    // TCMSFieldPosition
/** @var int - Sorting */
private int $sortOrder = 0  ) {}

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
public function getShopCategory(): \ChameleonSystem\CoreBundle\Entity\ShopCategory|null
{
    return $this->shopCategory;
}
public function setShopCategory(\ChameleonSystem\CoreBundle\Entity\ShopCategory|null $shopCategory): self
{
    $this->shopCategory = $shopCategory;
    $this->shopCategoryId = $shopCategory?->getId();

    return $this;
}
public function getShopCategoryId(): ?string
{
    return $this->shopCategoryId;
}
public function setShopCategoryId(?string $shopCategoryId): self
{
    $this->shopCategoryId = $shopCategoryId;
    // todo - load new id
    //$this->shopCategoryId = $?->getId();

    return $this;
}



  
    // TCMSFieldPosition
public function getSortOrder(): int
{
    return $this->sortOrder;
}
public function setSortOrder(int $sortOrder): self
{
    $this->sortOrder = $sortOrder;

    return $this;
}


  
    // TCMSFieldLookup
public function getShop(): \ChameleonSystem\CoreBundle\Entity\Shop|null
{
    return $this->shop;
}
public function setShop(\ChameleonSystem\CoreBundle\Entity\Shop|null $shop): self
{
    $this->shop = $shop;
    $this->shopId = $shop?->getId();

    return $this;
}
public function getShopId(): ?string
{
    return $this->shopId;
}
public function setShopId(?string $shopId): self
{
    $this->shopId = $shopId;
    // todo - load new id
    //$this->shopId = $?->getId();

    return $this;
}



  
}
