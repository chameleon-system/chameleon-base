<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgShopPaymentIpnTrigger {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\ShopPaymentHandlerGroup|null - Belongs to payment provider */
private \ChameleonSystem\CoreBundle\Entity\ShopPaymentHandlerGroup|null $shopPaymentHandlerGroup = null,
/** @var null|string - Belongs to payment provider */
private ?string $shopPaymentHandlerGroupId = null
, 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\PkgShopPaymentIpnMessageTrigger[] -  */
private \Doctrine\Common\Collections\Collection $pkgShopPaymentIpnMessageTriggerCollection = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldBoolean
/** @var bool - Active */
private bool $active = false, 
    // TCMSFieldURL
/** @var string - Target URL */
private string $targetUrl = '', 
    // TCMSFieldNumber
/** @var int - Timeout */
private int $timeoutSeconds = 30, 
    // TCMSFieldLookupMultiselect
/** @var \ChameleonSystem\CoreBundle\Entity\PkgShopPaymentIpnStatus[] - Status codes to be forwarded */
private \Doctrine\Common\Collections\Collection $pkgShopPaymentIpnStatusMlt = new \Doctrine\Common\Collections\ArrayCollection()  ) {}

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
public function getShopPaymentHandlerGroup(): \ChameleonSystem\CoreBundle\Entity\ShopPaymentHandlerGroup|null
{
    return $this->shopPaymentHandlerGroup;
}
public function setShopPaymentHandlerGroup(\ChameleonSystem\CoreBundle\Entity\ShopPaymentHandlerGroup|null $shopPaymentHandlerGroup): self
{
    $this->shopPaymentHandlerGroup = $shopPaymentHandlerGroup;
    $this->shopPaymentHandlerGroupId = $shopPaymentHandlerGroup?->getId();

    return $this;
}
public function getShopPaymentHandlerGroupId(): ?string
{
    return $this->shopPaymentHandlerGroupId;
}
public function setShopPaymentHandlerGroupId(?string $shopPaymentHandlerGroupId): self
{
    $this->shopPaymentHandlerGroupId = $shopPaymentHandlerGroupId;
    // todo - load new id
    //$this->shopPaymentHandlerGroupId = $?->getId();

    return $this;
}



  
    // TCMSFieldPropertyTable
public function getPkgShopPaymentIpnMessageTriggerCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->pkgShopPaymentIpnMessageTriggerCollection;
}
public function setPkgShopPaymentIpnMessageTriggerCollection(\Doctrine\Common\Collections\Collection $pkgShopPaymentIpnMessageTriggerCollection): self
{
    $this->pkgShopPaymentIpnMessageTriggerCollection = $pkgShopPaymentIpnMessageTriggerCollection;

    return $this;
}


  
    // TCMSFieldBoolean
public function isActive(): bool
{
    return $this->active;
}
public function setActive(bool $active): self
{
    $this->active = $active;

    return $this;
}


  
    // TCMSFieldURL
public function getTargetUrl(): string
{
    return $this->targetUrl;
}
public function setTargetUrl(string $targetUrl): self
{
    $this->targetUrl = $targetUrl;

    return $this;
}


  
    // TCMSFieldNumber
public function getTimeoutSeconds(): int
{
    return $this->timeoutSeconds;
}
public function setTimeoutSeconds(int $timeoutSeconds): self
{
    $this->timeoutSeconds = $timeoutSeconds;

    return $this;
}


  
    // TCMSFieldLookupMultiselect
public function getPkgShopPaymentIpnStatusMlt(): \Doctrine\Common\Collections\Collection
{
    return $this->pkgShopPaymentIpnStatusMlt;
}
public function setPkgShopPaymentIpnStatusMlt(\Doctrine\Common\Collections\Collection $pkgShopPaymentIpnStatusMlt): self
{
    $this->pkgShopPaymentIpnStatusMlt = $pkgShopPaymentIpnStatusMlt;

    return $this;
}


  
}
