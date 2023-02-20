<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\ShopOrder;
use ChameleonSystem\CoreBundle\Entity\ShopOrderStatusItem;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

class ShopOrderStatus {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldLookupParentID
/** @var ShopOrder|null - Belongs to order */
private ?ShopOrder $shopOrder = null
, 
    // TCMSFieldPropertyTable
/** @var Collection<int, shopOrderStatusItem> - Order status items */
private Collection $shopOrderStatusItemCollection = new ArrayCollection()
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
public function getShopOrder(): ?ShopOrder
{
    return $this->shopOrder;
}

public function setShopOrder(?ShopOrder $shopOrder): self
{
    $this->shopOrder = $shopOrder;

    return $this;
}


  
    // TCMSFieldPropertyTable
/**
* @return Collection<int, shopOrderStatusItem>
*/
public function getShopOrderStatusItemCollection(): Collection
{
    return $this->shopOrderStatusItemCollection;
}

public function addShopOrderStatusItemCollection(shopOrderStatusItem $shopOrderStatusItem): self
{
    if (!$this->shopOrderStatusItemCollection->contains($shopOrderStatusItem)) {
        $this->shopOrderStatusItemCollection->add($shopOrderStatusItem);
        $shopOrderStatusItem->setShopOrderStatus($this);
    }

    return $this;
}

public function removeShopOrderStatusItemCollection(shopOrderStatusItem $shopOrderStatusItem): self
{
    if ($this->shopOrderStatusItemCollection->removeElement($shopOrderStatusItem)) {
        // set the owning side to null (unless already changed)
        if ($shopOrderStatusItem->getShopOrderStatus() === $this) {
            $shopOrderStatusItem->setShopOrderStatus(null);
        }
    }

    return $this;
}


  
}
