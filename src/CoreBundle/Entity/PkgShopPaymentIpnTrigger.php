<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\ShopPaymentHandlerGroup;
use ChameleonSystem\CoreBundle\Entity\PkgShopPaymentIpnMessageTrigger;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

class PkgShopPaymentIpnTrigger {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldLookup
/** @var ShopPaymentHandlerGroup|null - Belongs to payment provider */
private ?ShopPaymentHandlerGroup $shopPaymentHandlerGroup = null
, 
    // TCMSFieldPropertyTable
/** @var Collection<int, pkgShopPaymentIpnMessageTrigger> -  */
private Collection $pkgShopPaymentIpnMessageTriggerCollection = new ArrayCollection()
, 
    // TCMSFieldVarchar
/** @var string - Target URL */
private string $targetUrl = '', 
    // TCMSFieldVarchar
/** @var string - Timeout */
private string $timeoutSeconds = '30'  ) {}

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
public function getShopPaymentHandlerGroup(): ?ShopPaymentHandlerGroup
{
    return $this->shopPaymentHandlerGroup;
}

public function setShopPaymentHandlerGroup(?ShopPaymentHandlerGroup $shopPaymentHandlerGroup): self
{
    $this->shopPaymentHandlerGroup = $shopPaymentHandlerGroup;

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
        $pkgShopPaymentIpnMessageTrigger->setPkgShopPaymentIpnTrigger($this);
    }

    return $this;
}

public function removePkgShopPaymentIpnMessageTriggerCollection(pkgShopPaymentIpnMessageTrigger $pkgShopPaymentIpnMessageTrigger): self
{
    if ($this->pkgShopPaymentIpnMessageTriggerCollection->removeElement($pkgShopPaymentIpnMessageTrigger)) {
        // set the owning side to null (unless already changed)
        if ($pkgShopPaymentIpnMessageTrigger->getPkgShopPaymentIpnTrigger() === $this) {
            $pkgShopPaymentIpnMessageTrigger->setPkgShopPaymentIpnTrigger(null);
        }
    }

    return $this;
}


  
    // TCMSFieldVarchar
public function getTargetUrl(): string
{
    return $this->targetUrl;
}
public function setTargetUrl(string $targetUrl): self
{
    $this->targetUrl = $targetUrl;

    return $this;
}


  
    // TCMSFieldVarchar
public function getTimeoutSeconds(): string
{
    return $this->timeoutSeconds;
}
public function setTimeoutSeconds(string $timeoutSeconds): self
{
    $this->timeoutSeconds = $timeoutSeconds;

    return $this;
}


  
}
