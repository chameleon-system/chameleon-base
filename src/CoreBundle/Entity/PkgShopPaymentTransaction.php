<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgShopPaymentTransaction {
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
/** @var \ChameleonSystem\CoreBundle\Entity\DataExtranetUser|null - Executed by user */
private \ChameleonSystem\CoreBundle\Entity\DataExtranetUser|null $dataExtranetUser = null,
/** @var null|string - Executed by user */
private ?string $dataExtranetUserId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\PkgShopPaymentTransactionType|null - Transaction type */
private \ChameleonSystem\CoreBundle\Entity\PkgShopPaymentTransactionType|null $pkgShopPaymentTransactionType = null,
/** @var null|string - Transaction type */
private ?string $pkgShopPaymentTransactionTypeId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsUser|null - Executed by CMS user */
private \ChameleonSystem\CoreBundle\Entity\CmsUser|null $cmsUser = null,
/** @var null|string - Executed by CMS user */
private ?string $cmsUserId = null
, 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\PkgShopPaymentTransactionPosition[] - Positions */
private \Doctrine\Common\Collections\Collection $pkgShopPaymentTransactionPositionCollection = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldCreatedTimestamp
/** @var \DateTime|null - Created on */
private \DateTime|null $datecreated = null, 
    // TCMSFieldVarchar
/** @var string - Executed via IP */
private string $ip = '', 
    // TCMSFieldDecimal
/** @var float - Value */
private float $amount = 0, 
    // TCMSFieldVarchar
/** @var string - Context */
private string $context = '', 
    // TCMSFieldNumber
/** @var int - Sequence number */
private int $sequenceNumber = 0, 
    // TCMSFieldBoolean
/** @var bool - Confirmed */
private bool $confirmed = false, 
    // TCMSFieldDateTime
/** @var \DateTime|null - Confirmed on */
private \DateTime|null $confirmedDate = null  ) {}

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



  
    // TCMSFieldLookup
public function getDataExtranetUser(): \ChameleonSystem\CoreBundle\Entity\DataExtranetUser|null
{
    return $this->dataExtranetUser;
}
public function setDataExtranetUser(\ChameleonSystem\CoreBundle\Entity\DataExtranetUser|null $dataExtranetUser): self
{
    $this->dataExtranetUser = $dataExtranetUser;
    $this->dataExtranetUserId = $dataExtranetUser?->getId();

    return $this;
}
public function getDataExtranetUserId(): ?string
{
    return $this->dataExtranetUserId;
}
public function setDataExtranetUserId(?string $dataExtranetUserId): self
{
    $this->dataExtranetUserId = $dataExtranetUserId;
    // todo - load new id
    //$this->dataExtranetUserId = $?->getId();

    return $this;
}



  
    // TCMSFieldPropertyTable
public function getPkgShopPaymentTransactionPositionCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->pkgShopPaymentTransactionPositionCollection;
}
public function setPkgShopPaymentTransactionPositionCollection(\Doctrine\Common\Collections\Collection $pkgShopPaymentTransactionPositionCollection): self
{
    $this->pkgShopPaymentTransactionPositionCollection = $pkgShopPaymentTransactionPositionCollection;

    return $this;
}


  
    // TCMSFieldLookup
public function getPkgShopPaymentTransactionType(): \ChameleonSystem\CoreBundle\Entity\PkgShopPaymentTransactionType|null
{
    return $this->pkgShopPaymentTransactionType;
}
public function setPkgShopPaymentTransactionType(\ChameleonSystem\CoreBundle\Entity\PkgShopPaymentTransactionType|null $pkgShopPaymentTransactionType): self
{
    $this->pkgShopPaymentTransactionType = $pkgShopPaymentTransactionType;
    $this->pkgShopPaymentTransactionTypeId = $pkgShopPaymentTransactionType?->getId();

    return $this;
}
public function getPkgShopPaymentTransactionTypeId(): ?string
{
    return $this->pkgShopPaymentTransactionTypeId;
}
public function setPkgShopPaymentTransactionTypeId(?string $pkgShopPaymentTransactionTypeId): self
{
    $this->pkgShopPaymentTransactionTypeId = $pkgShopPaymentTransactionTypeId;
    // todo - load new id
    //$this->pkgShopPaymentTransactionTypeId = $?->getId();

    return $this;
}



  
    // TCMSFieldLookup
public function getCmsUser(): \ChameleonSystem\CoreBundle\Entity\CmsUser|null
{
    return $this->cmsUser;
}
public function setCmsUser(\ChameleonSystem\CoreBundle\Entity\CmsUser|null $cmsUser): self
{
    $this->cmsUser = $cmsUser;
    $this->cmsUserId = $cmsUser?->getId();

    return $this;
}
public function getCmsUserId(): ?string
{
    return $this->cmsUserId;
}
public function setCmsUserId(?string $cmsUserId): self
{
    $this->cmsUserId = $cmsUserId;
    // todo - load new id
    //$this->cmsUserId = $?->getId();

    return $this;
}



  
    // TCMSFieldCreatedTimestamp
public function getDatecreated(): \DateTime|null
{
    return $this->datecreated;
}
public function setDatecreated(\DateTime|null $datecreated): self
{
    $this->datecreated = $datecreated;

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


  
    // TCMSFieldDecimal
public function getAmount(): float
{
    return $this->amount;
}
public function setAmount(float $amount): self
{
    $this->amount = $amount;

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


  
    // TCMSFieldNumber
public function getSequenceNumber(): int
{
    return $this->sequenceNumber;
}
public function setSequenceNumber(int $sequenceNumber): self
{
    $this->sequenceNumber = $sequenceNumber;

    return $this;
}


  
    // TCMSFieldBoolean
public function isConfirmed(): bool
{
    return $this->confirmed;
}
public function setConfirmed(bool $confirmed): self
{
    $this->confirmed = $confirmed;

    return $this;
}


  
    // TCMSFieldDateTime
public function getConfirmedDate(): \DateTime|null
{
    return $this->confirmedDate;
}
public function setConfirmedDate(\DateTime|null $confirmedDate): self
{
    $this->confirmedDate = $confirmedDate;

    return $this;
}


  
}
