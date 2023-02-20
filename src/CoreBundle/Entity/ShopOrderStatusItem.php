<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\ShopOrderStatus;

class ShopOrderStatusItem {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldLookupParentID
/** @var ShopOrderStatus|null - Belongs to status */
private ?ShopOrderStatus $shopOrderStatus = null
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
    // TCMSFieldLookupParentID
public function getShopOrderStatus(): ?ShopOrderStatus
{
    return $this->shopOrderStatus;
}

public function setShopOrderStatus(?ShopOrderStatus $shopOrderStatus): self
{
    $this->shopOrderStatus = $shopOrderStatus;

    return $this;
}


  
}
