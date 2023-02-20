<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgShopPaymentIpnMessageTrigger {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\PkgShopPaymentIpnTrigger|null - Trigger */
private \ChameleonSystem\CoreBundle\Entity\PkgShopPaymentIpnTrigger|null $pkgShopPaymentIpnTrigger = null,
/** @var null|string - Trigger */
private ?string $pkgShopPaymentIpnTriggerId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\PkgShopPaymentIpnMessage|null - IPN Message */
private \ChameleonSystem\CoreBundle\Entity\PkgShopPaymentIpnMessage|null $pkgShopPaymentIpnMessage = null,
/** @var null|string - IPN Message */
private ?string $pkgShopPaymentIpnMessageId = null
, 
    // TCMSFieldCreatedTimestamp
/** @var \DateTime|null - Created on */
private \DateTime|null $datecreated = null, 
    // TCMSFieldBoolean
/** @var bool - Processed */
private bool $done = false, 
    // TCMSFieldDateTime
/** @var \DateTime|null - Processed on */
private \DateTime|null $doneDate = null, 
    // TCMSFieldBoolean
/** @var bool - Successful */
private bool $success = false, 
    // TCMSFieldNumber
/** @var int - Number of attempts */
private int $attemptCount = 0, 
    // TCMSFieldDateTime
/** @var \DateTime|null - Next attempt on */
private \DateTime|null $nextAttempt = null, 
    // TCMSFieldText
/** @var string - Log */
private string $log = ''  ) {}

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
public function getPkgShopPaymentIpnTrigger(): \ChameleonSystem\CoreBundle\Entity\PkgShopPaymentIpnTrigger|null
{
    return $this->pkgShopPaymentIpnTrigger;
}
public function setPkgShopPaymentIpnTrigger(\ChameleonSystem\CoreBundle\Entity\PkgShopPaymentIpnTrigger|null $pkgShopPaymentIpnTrigger): self
{
    $this->pkgShopPaymentIpnTrigger = $pkgShopPaymentIpnTrigger;
    $this->pkgShopPaymentIpnTriggerId = $pkgShopPaymentIpnTrigger?->getId();

    return $this;
}
public function getPkgShopPaymentIpnTriggerId(): ?string
{
    return $this->pkgShopPaymentIpnTriggerId;
}
public function setPkgShopPaymentIpnTriggerId(?string $pkgShopPaymentIpnTriggerId): self
{
    $this->pkgShopPaymentIpnTriggerId = $pkgShopPaymentIpnTriggerId;
    // todo - load new id
    //$this->pkgShopPaymentIpnTriggerId = $?->getId();

    return $this;
}



  
    // TCMSFieldLookup
public function getPkgShopPaymentIpnMessage(): \ChameleonSystem\CoreBundle\Entity\PkgShopPaymentIpnMessage|null
{
    return $this->pkgShopPaymentIpnMessage;
}
public function setPkgShopPaymentIpnMessage(\ChameleonSystem\CoreBundle\Entity\PkgShopPaymentIpnMessage|null $pkgShopPaymentIpnMessage): self
{
    $this->pkgShopPaymentIpnMessage = $pkgShopPaymentIpnMessage;
    $this->pkgShopPaymentIpnMessageId = $pkgShopPaymentIpnMessage?->getId();

    return $this;
}
public function getPkgShopPaymentIpnMessageId(): ?string
{
    return $this->pkgShopPaymentIpnMessageId;
}
public function setPkgShopPaymentIpnMessageId(?string $pkgShopPaymentIpnMessageId): self
{
    $this->pkgShopPaymentIpnMessageId = $pkgShopPaymentIpnMessageId;
    // todo - load new id
    //$this->pkgShopPaymentIpnMessageId = $?->getId();

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


  
    // TCMSFieldBoolean
public function isDone(): bool
{
    return $this->done;
}
public function setDone(bool $done): self
{
    $this->done = $done;

    return $this;
}


  
    // TCMSFieldDateTime
public function getDoneDate(): \DateTime|null
{
    return $this->doneDate;
}
public function setDoneDate(\DateTime|null $doneDate): self
{
    $this->doneDate = $doneDate;

    return $this;
}


  
    // TCMSFieldBoolean
public function isSuccess(): bool
{
    return $this->success;
}
public function setSuccess(bool $success): self
{
    $this->success = $success;

    return $this;
}


  
    // TCMSFieldNumber
public function getAttemptCount(): int
{
    return $this->attemptCount;
}
public function setAttemptCount(int $attemptCount): self
{
    $this->attemptCount = $attemptCount;

    return $this;
}


  
    // TCMSFieldDateTime
public function getNextAttempt(): \DateTime|null
{
    return $this->nextAttempt;
}
public function setNextAttempt(\DateTime|null $nextAttempt): self
{
    $this->nextAttempt = $nextAttempt;

    return $this;
}


  
    // TCMSFieldText
public function getLog(): string
{
    return $this->log;
}
public function setLog(string $log): self
{
    $this->log = $log;

    return $this;
}


  
}
