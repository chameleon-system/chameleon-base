<?php
namespace ChameleonSystem\CoreBundle\Entity;

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
    // TCMSFieldPropertyTable
/** @var Collection<int, shopVoucher> - Vouchers belonging to the series */
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


  
    // TCMSFieldPropertyTable
/**
* @return Collection<int, shopVoucher>
*/
public function getShopVoucherCollection(): Collection
{
    return $this->shopVoucherCollection;
}

public function addShopVoucherCollection(shopVoucher $shopVoucher): self
{
    if (!$this->shopVoucherCollection->contains($shopVoucher)) {
        $this->shopVoucherCollection->add($shopVoucher);
        $shopVoucher->setShopVoucherSeries($this);
    }

    return $this;
}

public function removeShopVoucherCollection(shopVoucher $shopVoucher): self
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
