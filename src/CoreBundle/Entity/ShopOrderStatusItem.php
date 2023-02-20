<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopOrderStatusItem {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\ShopOrderStatus|null - Belongs to status */
private \ChameleonSystem\CoreBundle\Entity\ShopOrderStatus|null $shopOrderStatus = null,
/** @var null|string - Belongs to status */
private ?string $shopOrderStatusId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\ShopOrderItem|null - Product */
private \ChameleonSystem\CoreBundle\Entity\ShopOrderItem|null $shopOrderItem = null,
/** @var null|string - Product */
private ?string $shopOrderItemId = null
, 
    // TCMSFieldDecimal
/** @var float - Amount */
private float $amount = 0  ) {}

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
public function getShopOrderStatus(): \ChameleonSystem\CoreBundle\Entity\ShopOrderStatus|null
{
    return $this->shopOrderStatus;
}
public function setShopOrderStatus(\ChameleonSystem\CoreBundle\Entity\ShopOrderStatus|null $shopOrderStatus): self
{
    $this->shopOrderStatus = $shopOrderStatus;
    $this->shopOrderStatusId = $shopOrderStatus?->getId();

    return $this;
}
public function getShopOrderStatusId(): ?string
{
    return $this->shopOrderStatusId;
}
public function setShopOrderStatusId(?string $shopOrderStatusId): self
{
    $this->shopOrderStatusId = $shopOrderStatusId;
    // todo - load new id
    //$this->shopOrderStatusId = $?->getId();

    return $this;
}



  
    // TCMSFieldLookup
public function getShopOrderItem(): \ChameleonSystem\CoreBundle\Entity\ShopOrderItem|null
{
    return $this->shopOrderItem;
}
public function setShopOrderItem(\ChameleonSystem\CoreBundle\Entity\ShopOrderItem|null $shopOrderItem): self
{
    $this->shopOrderItem = $shopOrderItem;
    $this->shopOrderItemId = $shopOrderItem?->getId();

    return $this;
}
public function getShopOrderItemId(): ?string
{
    return $this->shopOrderItemId;
}
public function setShopOrderItemId(?string $shopOrderItemId): self
{
    $this->shopOrderItemId = $shopOrderItemId;
    // todo - load new id
    //$this->shopOrderItemId = $?->getId();

    return $this;
}



  
    // TCMSFieldDecimal
public function getAmount(): float
{
    return $this->amount;
}
public function setAmount(float $amount): self
{
    $this->amount = $amount;

    return $this;
}


  
}
