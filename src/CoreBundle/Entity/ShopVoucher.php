<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\ShopVoucherSeries;
use ChameleonSystem\CoreBundle\Entity\ShopVoucherUse;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

class ShopVoucher {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldLookup
/** @var ShopVoucherSeries|null - Belongs to voucher series */
private ?ShopVoucherSeries $shopVoucherSeries = null
, 
    // TCMSFieldVarchar
/** @var string - Code */
private string $code = '', 
    // TCMSFieldPropertyTable
/** @var Collection<int, shopVoucherUse> - Voucher usages */
private Collection $shopVoucherUseCollection = new ArrayCollection()
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
    // TCMSFieldLookup
public function getShopVoucherSeries(): ?ShopVoucherSeries
{
    return $this->shopVoucherSeries;
}

public function setShopVoucherSeries(?ShopVoucherSeries $shopVoucherSeries): self
{
    $this->shopVoucherSeries = $shopVoucherSeries;

    return $this;
}


  
    // TCMSFieldVarchar
public function getCode(): string
{
    return $this->code;
}
public function setCode(string $code): self
{
    $this->code = $code;

    return $this;
}


  
    // TCMSFieldPropertyTable
/**
* @return Collection<int, shopVoucherUse>
*/
public function getShopVoucherUseCollection(): Collection
{
    return $this->shopVoucherUseCollection;
}

public function addShopVoucherUseCollection(shopVoucherUse $shopVoucherUse): self
{
    if (!$this->shopVoucherUseCollection->contains($shopVoucherUse)) {
        $this->shopVoucherUseCollection->add($shopVoucherUse);
        $shopVoucherUse->setShopVoucher($this);
    }

    return $this;
}

public function removeShopVoucherUseCollection(shopVoucherUse $shopVoucherUse): self
{
    if ($this->shopVoucherUseCollection->removeElement($shopVoucherUse)) {
        // set the owning side to null (unless already changed)
        if ($shopVoucherUse->getShopVoucher() === $this) {
            $shopVoucherUse->setShopVoucher(null);
        }
    }

    return $this;
}


  
}
