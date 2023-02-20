<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\ShopOrderItem;

class ShopOrderBundleArticle {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldLookupParentID
/** @var ShopOrderItem|null - Bundle articles of the order */
private ?ShopOrderItem $shopOrderItem = null
, 
    // TCMSFieldVarchar
/** @var string - Units */
private string $amount = ''  ) {}

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
    // TCMSFieldLookupParentID
public function getShopOrderItem(): ?ShopOrderItem
{
    return $this->shopOrderItem;
}

public function setShopOrderItem(?ShopOrderItem $shopOrderItem): self
{
    $this->shopOrderItem = $shopOrderItem;

    return $this;
}


  
    // TCMSFieldVarchar
public function getAmount(): string
{
    return $this->amount;
}
public function setAmount(string $amount): self
{
    $this->amount = $amount;

    return $this;
}


  
}
