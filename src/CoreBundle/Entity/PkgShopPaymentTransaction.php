<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\ShopOrder;
use ChameleonSystem\CoreBundle\Entity\PkgShopPaymentTransactionPosition;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

class PkgShopPaymentTransaction {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldLookupParentID
/** @var ShopOrder|null - Belongs to order */
private ?ShopOrder $shopOrder = null
, 
    // TCMSFieldPropertyTable
/** @var Collection<int, pkgShopPaymentTransactionPosition> - Positions */
private Collection $pkgShopPaymentTransactionPositionCollection = new ArrayCollection()
, 
    // TCMSFieldVarchar
/** @var string - Executed via IP */
private string $ip = '', 
    // TCMSFieldVarchar
/** @var string - Context */
private string $context = '', 
    // TCMSFieldVarchar
/** @var string - Sequence number */
private string $sequenceNumber = ''  ) {}

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
* @return Collection<int, pkgShopPaymentTransactionPosition>
*/
public function getPkgShopPaymentTransactionPositionCollection(): Collection
{
    return $this->pkgShopPaymentTransactionPositionCollection;
}

public function addPkgShopPaymentTransactionPositionCollection(pkgShopPaymentTransactionPosition $pkgShopPaymentTransactionPosition): self
{
    if (!$this->pkgShopPaymentTransactionPositionCollection->contains($pkgShopPaymentTransactionPosition)) {
        $this->pkgShopPaymentTransactionPositionCollection->add($pkgShopPaymentTransactionPosition);
        $pkgShopPaymentTransactionPosition->setPkgShopPaymentTransaction($this);
    }

    return $this;
}

public function removePkgShopPaymentTransactionPositionCollection(pkgShopPaymentTransactionPosition $pkgShopPaymentTransactionPosition): self
{
    if ($this->pkgShopPaymentTransactionPositionCollection->removeElement($pkgShopPaymentTransactionPosition)) {
        // set the owning side to null (unless already changed)
        if ($pkgShopPaymentTransactionPosition->getPkgShopPaymentTransaction() === $this) {
            $pkgShopPaymentTransactionPosition->setPkgShopPaymentTransaction(null);
        }
    }

    return $this;
}


  
    // TCMSFieldVarchar
public function getIp(): string
{
    return $this->ip;
}
public function setIp(string $ip): self
{
    $this->ip = $ip;

    return $this;
}


  
    // TCMSFieldVarchar
public function getContext(): string
{
    return $this->context;
}
public function setContext(string $context): self
{
    $this->context = $context;

    return $this;
}


  
    // TCMSFieldVarchar
public function getSequenceNumber(): string
{
    return $this->sequenceNumber;
}
public function setSequenceNumber(string $sequenceNumber): self
{
    $this->sequenceNumber = $sequenceNumber;

    return $this;
}


  
}
