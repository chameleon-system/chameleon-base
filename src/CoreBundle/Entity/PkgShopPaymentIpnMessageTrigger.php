<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\PkgShopPaymentIpnTrigger;
use ChameleonSystem\CoreBundle\Entity\PkgShopPaymentIpnMessage;

class PkgShopPaymentIpnMessageTrigger {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldLookup
/** @var PkgShopPaymentIpnTrigger|null - Trigger */
private ?PkgShopPaymentIpnTrigger $pkgShopPaymentIpnTrigger = null
, 
    // TCMSFieldLookup
/** @var PkgShopPaymentIpnMessage|null - IPN Message */
private ?PkgShopPaymentIpnMessage $pkgShopPaymentIpnMessage = null
, 
    // TCMSFieldVarchar
/** @var string - Number of attempts */
private string $attemptCount = '0'  ) {}

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
public function getPkgShopPaymentIpnTrigger(): ?PkgShopPaymentIpnTrigger
{
    return $this->pkgShopPaymentIpnTrigger;
}

public function setPkgShopPaymentIpnTrigger(?PkgShopPaymentIpnTrigger $pkgShopPaymentIpnTrigger): self
{
    $this->pkgShopPaymentIpnTrigger = $pkgShopPaymentIpnTrigger;

    return $this;
}


  
    // TCMSFieldLookup
public function getPkgShopPaymentIpnMessage(): ?PkgShopPaymentIpnMessage
{
    return $this->pkgShopPaymentIpnMessage;
}

public function setPkgShopPaymentIpnMessage(?PkgShopPaymentIpnMessage $pkgShopPaymentIpnMessage): self
{
    $this->pkgShopPaymentIpnMessage = $pkgShopPaymentIpnMessage;

    return $this;
}


  
    // TCMSFieldVarchar
public function getAttemptCount(): string
{
    return $this->attemptCount;
}
public function setAttemptCount(string $attemptCount): self
{
    $this->attemptCount = $attemptCount;

    return $this;
}


  
}
