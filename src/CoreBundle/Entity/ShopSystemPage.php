<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopSystemPage {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\Shop|null - Belongs to shop */
private \ChameleonSystem\CoreBundle\Entity\Shop|null $shop = null,
/** @var null|string - Belongs to shop */
private ?string $shopId = null
, 
    // TCMSFieldVarchar
/** @var string - Internal system name */
private string $nameInternal = '', 
    // TCMSFieldVarchar
/** @var string - Display name */
private string $name = '', 
    // TCMSFieldTreeNode
/** @var \ChameleonSystem\CoreBundle\Entity\CmsTree - Navigation item (node) */
private \ChameleonSystem\CoreBundle\Entity\CmsTree|null $cmsTreeId = null  ) {}

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



  
    // TCMSFieldVarchar
public function getNameInternal(): string
{
    return $this->nameInternal;
}
public function setNameInternal(string $nameInternal): self
{
    $this->nameInternal = $nameInternal;

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


  
    // TCMSFieldTreeNode
public function getCmsTreeId(): \ChameleonSystem\CoreBundle\Entity\CmsTree|null
{
    return $this->cmsTreeId;
}
public function setCmsTreeId(\ChameleonSystem\CoreBundle\Entity\CmsTree|null $cmsTreeId): self
{
    $this->cmsTreeId = $cmsTreeId;

    return $this;
}


  
}
