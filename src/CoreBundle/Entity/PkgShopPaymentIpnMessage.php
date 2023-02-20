<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\PkgShopPaymentIpnMessageTrigger;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use ChameleonSystem\CoreBundle\Entity\ShopOrder;
use ChameleonSystem\CoreBundle\Entity\ShopPaymentHandlerGroup;

class PkgShopPaymentIpnMessage {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldPropertyTable
/** @var Collection<int, pkgShopPaymentIpnMessageTrigger> - Forwarding logs */
private Collection $pkgShopPaymentIpnMessageTriggerCollection = new ArrayCollection()
, 
    // TCMSFieldLookupParentID
/** @var ShopOrder|null - Belongs to order (ID) */
private ?ShopOrder $shopOrder = null
, 
    // TCMSFieldLookupParentID
/** @var ShopPaymentHandlerGroup|null - Payment provider */
private ?ShopPaymentHandlerGroup $shopPaymentHandlerGroup = null
, 
    // TCMSFieldVarchar
/** @var string - Type of error */
private string $errorType = '', 
    // TCMSFieldVarchar
/** @var string - IP */
private string $ip = '', 
    // TCMSFieldVarchar
/** @var string - Request URL */
private string $requestUrl = ''  ) {}

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
    // TCMSFieldPropertyTable
/**
* @return Collection<int, pkgShopPaymentIpnMessageTrigger>
*/
public function getPkgShopPaymentIpnMessageTriggerCollection(): Collection
{
    return $this->pkgShopPaymentIpnMessageTriggerCollection;
}

public function addPkgShopPaymentIpnMessageTriggerCollection(pkgShopPaymentIpnMessageTrigger $pkgShopPaymentIpnMessageTrigger): self
{
    if (!$this->pkgShopPaymentIpnMessageTriggerCollection->contains($pkgShopPaymentIpnMessageTrigger)) {
        $this->pkgShopPaymentIpnMessageTriggerCollection->add($pkgShopPaymentIpnMessageTrigger);
        $pkgShopPaymentIpnMessageTrigger->setPkgShopPaymentIpnMessage($this);
    }

    return $this;
}

public function removePkgShopPaymentIpnMessageTriggerCollection(pkgShopPaymentIpnMessageTrigger $pkgShopPaymentIpnMessageTrigger): self
{
    if ($this->pkgShopPaymentIpnMessageTriggerCollection->removeElement($pkgShopPaymentIpnMessageTrigger)) {
        // set the owning side to null (unless already changed)
        if ($pkgShopPaymentIpnMessageTrigger->getPkgShopPaymentIpnMessage() === $this) {
            $pkgShopPaymentIpnMessageTrigger->setPkgShopPaymentIpnMessage(null);
        }
    }

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


  
    // TCMSFieldLookupParentID
public function getShopPaymentHandlerGroup(): ?ShopPaymentHandlerGroup
{
    return $this->shopPaymentHandlerGroup;
}

public function setShopPaymentHandlerGroup(?ShopPaymentHandlerGroup $shopPaymentHandlerGroup): self
{
    $this->shopPaymentHandlerGroup = $shopPaymentHandlerGroup;

    return $this;
}


  
    // TCMSFieldVarchar
public function getErrorType(): string
{
    return $this->errorType;
}
public function setErrorType(string $errorType): self
{
    $this->errorType = $errorType;

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
public function getRequestUrl(): string
{
    return $this->requestUrl;
}
public function setRequestUrl(string $requestUrl): self
{
    $this->requestUrl = $requestUrl;

    return $this;
}


  
}
