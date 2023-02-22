<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\ShopVoucherSeriesSponsor;
use ChameleonSystem\CoreBundle\Entity\ShopVat;
use ChameleonSystem\CoreBundle\Entity\ShopVoucher;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

class ShopVoucherSeries {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - Name */
private string $name = '', 
    // TCMSFieldLookup
/** @var ShopVoucherSeriesSponsor|null - Voucher sponsor */
private ?ShopVoucherSeriesSponsor $shopVoucherSeriesSponsor = null
, 
    // TCMSFieldLookup
/** @var ShopVat|null - VAT group */
private ?ShopVat $shopVat = null
, 
    // TCMSFieldPropertyTable
/** @var Collection<int, ShopVoucher> - Vouchers belonging to the series */
private Collection $shopVoucherCollection = new ArrayCollection()
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
public function getShopVoucherSeriesSponsor(): ?ShopVoucherSeriesSponsor
{
    return $this->shopVoucherSeriesSponsor;
}

public function setShopVoucherSeriesSponsor(?ShopVoucherSeriesSponsor $shopVoucherSeriesSponsor): self
{
    $this->shopVoucherSeriesSponsor = $shopVoucherSeriesSponsor;

    return $this;
}


  
    // TCMSFieldLookup
public function getShopVat(): ?ShopVat
{
    return $this->shopVat;
}

public function setShopVat(?ShopVat $shopVat): self
{
    $this->shopVat = $shopVat;

    return $this;
}


  
    // TCMSFieldPropertyTable
/**
* @return Collection<int, ShopVoucher>
*/
public function getShopVoucherCollection(): Collection
{
    return $this->shopVoucherCollection;
}

public function addShopVoucherCollection(ShopVoucher $shopVoucher): self
{
    if (!$this->shopVoucherCollection->contains($shopVoucher)) {
        $this->shopVoucherCollection->add($shopVoucher);
        $shopVoucher->setShopVoucherSeries($this);
    }

    return $this;
}

public function removeShopVoucherCollection(ShopVoucher $shopVoucher): self
{
    if ($this->shopVoucherCollection->removeElement($shopVoucher)) {
        // set the owning side to null (unless already changed)
        if ($shopVoucher->getShopVoucherSeries() === $this) {
            $shopVoucher->setShopVoucherSeries(null);
        }
    }

    return $this;
}


  
}
