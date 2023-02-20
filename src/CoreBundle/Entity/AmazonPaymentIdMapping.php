<?php
namespace ChameleonSystem\CoreBundle\Entity;

class AmazonPaymentIdMapping {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\ShopOrder|null - Belongs to order */
private \ChameleonSystem\CoreBundle\Entity\ShopOrder|null $shopOrder = null,
/** @var null|string - Belongs to order */
private ?string $shopOrderId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\PkgShopPaymentTransaction|null - Belongs to transaction */
private \ChameleonSystem\CoreBundle\Entity\PkgShopPaymentTransaction|null $pkgShopPaymentTransaction = null,
/** @var null|string - Belongs to transaction */
private ?string $pkgShopPaymentTransactionId = null
, 
    // TCMSFieldVarchar
/** @var string - Amazon order reference ID */
private string $amazonOrderReferenceId = '', 
    // TCMSFieldVarchar
/** @var string - Local reference ID */
private string $localId = '', 
    // TCMSFieldVarchar
/** @var string - Amazon ID */
private string $amazonId = '', 
    // TCMSFieldDecimal
/** @var float - Value */
private float $value = 0, 
    // TCMSFieldNumber
/** @var int - Type */
private int $type = 0, 
    // TCMSFieldNumber
/** @var int - Request mode */
private int $requestMode = 1, 
    // TCMSFieldBoolean
/** @var bool - CaptureNow */
private bool $captureNow = false  ) {}

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
public function getShopOrder(): \ChameleonSystem\CoreBundle\Entity\ShopOrder|null
{
    return $this->shopOrder;
}
public function setShopOrder(\ChameleonSystem\CoreBundle\Entity\ShopOrder|null $shopOrder): self
{
    $this->shopOrder = $shopOrder;
    $this->shopOrderId = $shopOrder?->getId();

    return $this;
}
public function getShopOrderId(): ?string
{
    return $this->shopOrderId;
}
public function setShopOrderId(?string $shopOrderId): self
{
    $this->shopOrderId = $shopOrderId;
    // todo - load new id
    //$this->shopOrderId = $?->getId();

    return $this;
}



  
    // TCMSFieldVarchar
public function getAmazonOrderReferenceId(): string
{
    return $this->amazonOrderReferenceId;
}
public function setAmazonOrderReferenceId(string $amazonOrderReferenceId): self
{
    $this->amazonOrderReferenceId = $amazonOrderReferenceId;

    return $this;
}


  
    // TCMSFieldVarchar
public function getLocalId(): string
{
    return $this->localId;
}
public function setLocalId(string $localId): self
{
    $this->localId = $localId;

    return $this;
}


  
    // TCMSFieldVarchar
public function getAmazonId(): string
{
    return $this->amazonId;
}
public function setAmazonId(string $amazonId): self
{
    $this->amazonId = $amazonId;

    return $this;
}


  
    // TCMSFieldDecimal
public function getValue(): float
{
    return $this->value;
}
public function setValue(float $value): self
{
    $this->value = $value;

    return $this;
}


  
    // TCMSFieldNumber
public function getType(): int
{
    return $this->type;
}
public function setType(int $type): self
{
    $this->type = $type;

    return $this;
}


  
    // TCMSFieldNumber
public function getRequestMode(): int
{
    return $this->requestMode;
}
public function setRequestMode(int $requestMode): self
{
    $this->requestMode = $requestMode;

    return $this;
}


  
    // TCMSFieldBoolean
public function isCaptureNow(): bool
{
    return $this->captureNow;
}
public function setCaptureNow(bool $captureNow): self
{
    $this->captureNow = $captureNow;

    return $this;
}


  
    // TCMSFieldLookup
public function getPkgShopPaymentTransaction(): \ChameleonSystem\CoreBundle\Entity\PkgShopPaymentTransaction|null
{
    return $this->pkgShopPaymentTransaction;
}
public function setPkgShopPaymentTransaction(\ChameleonSystem\CoreBundle\Entity\PkgShopPaymentTransaction|null $pkgShopPaymentTransaction): self
{
    $this->pkgShopPaymentTransaction = $pkgShopPaymentTransaction;
    $this->pkgShopPaymentTransactionId = $pkgShopPaymentTransaction?->getId();

    return $this;
}
public function getPkgShopPaymentTransactionId(): ?string
{
    return $this->pkgShopPaymentTransactionId;
}
public function setPkgShopPaymentTransactionId(?string $pkgShopPaymentTransactionId): self
{
    $this->pkgShopPaymentTransactionId = $pkgShopPaymentTransactionId;
    // todo - load new id
    //$this->pkgShopPaymentTransactionId = $?->getId();

    return $this;
}



  
}
