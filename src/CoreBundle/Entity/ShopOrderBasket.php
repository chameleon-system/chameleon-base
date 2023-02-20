<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopOrderBasket {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\ShopOrder|null - Order */
private \ChameleonSystem\CoreBundle\Entity\ShopOrder|null $shopOrder = null,
/** @var null|string - Order */
private ?string $shopOrderId = null
, 
    // TCMSFieldVarcharUnique
/** @var string - Basket cart ID (will also be included in the order) */
private string $orderIdent = '', 
    // TCMSFieldVarchar
/** @var string - Session ID */
private string $sessionId = '', 
    // TCMSFieldNumber
/** @var int - Created on */
private int $datecreated = 0, 
    // TCMSFieldNumber
/** @var int - Last changed */
private int $lastmodified = 0, 
    // TCMSFieldText
/** @var string - Basket */
private string $rawdataBasket = '', 
    // TCMSFieldText
/** @var string - User data */
private string $rawdataUser = '', 
    // TCMSFieldText
/** @var string - Session */
private string $rawdataSession = '', 
    // TCMSFieldVarchar
/** @var string - Last update in step */
private string $updateStepname = '', 
    // TCMSFieldBoolean
/** @var bool - Processed */
private bool $processed = false  ) {}

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
    // TCMSFieldVarcharUnique
public function getOrderIdent(): string
{
    return $this->orderIdent;
}
public function setOrderIdent(string $orderIdent): self
{
    $this->orderIdent = $orderIdent;

    return $this;
}


  
    // TCMSFieldVarchar
public function getSessionId(): string
{
    return $this->sessionId;
}
public function setSessionId(string $sessionId): self
{
    $this->sessionId = $sessionId;

    return $this;
}


  
    // TCMSFieldNumber
public function getDatecreated(): int
{
    return $this->datecreated;
}
public function setDatecreated(int $datecreated): self
{
    $this->datecreated = $datecreated;

    return $this;
}


  
    // TCMSFieldNumber
public function getLastmodified(): int
{
    return $this->lastmodified;
}
public function setLastmodified(int $lastmodified): self
{
    $this->lastmodified = $lastmodified;

    return $this;
}


  
    // TCMSFieldText
public function getRawdataBasket(): string
{
    return $this->rawdataBasket;
}
public function setRawdataBasket(string $rawdataBasket): self
{
    $this->rawdataBasket = $rawdataBasket;

    return $this;
}


  
    // TCMSFieldText
public function getRawdataUser(): string
{
    return $this->rawdataUser;
}
public function setRawdataUser(string $rawdataUser): self
{
    $this->rawdataUser = $rawdataUser;

    return $this;
}


  
    // TCMSFieldText
public function getRawdataSession(): string
{
    return $this->rawdataSession;
}
public function setRawdataSession(string $rawdataSession): self
{
    $this->rawdataSession = $rawdataSession;

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
public function getUpdateStepname(): string
{
    return $this->updateStepname;
}
public function setUpdateStepname(string $updateStepname): self
{
    $this->updateStepname = $updateStepname;

    return $this;
}


  
    // TCMSFieldBoolean
public function isProcessed(): bool
{
    return $this->processed;
}
public function setProcessed(bool $processed): self
{
    $this->processed = $processed;

    return $this;
}


  
}
