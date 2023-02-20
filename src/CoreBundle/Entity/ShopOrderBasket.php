<?php
namespace ChameleonSystem\CoreBundle\Entity;


class ShopOrderBasket {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - Basket cart ID (will also be included in the order) */
private string $orderIdent = '', 
    // TCMSFieldVarchar
/** @var string - Session ID */
private string $sessionId = '', 
    // TCMSFieldVarchar
/** @var string - Created on */
private string $datecreated = '', 
    // TCMSFieldVarchar
/** @var string - Last changed */
private string $lastmodified = '', 
    // TCMSFieldVarchar
/** @var string - Last update in step */
private string $updateStepname = ''  ) {}

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


  
    // TCMSFieldVarchar
public function getDatecreated(): string
{
    return $this->datecreated;
}
public function setDatecreated(string $datecreated): self
{
    $this->datecreated = $datecreated;

    return $this;
}


  
    // TCMSFieldVarchar
public function getLastmodified(): string
{
    return $this->lastmodified;
}
public function setLastmodified(string $lastmodified): self
{
    $this->lastmodified = $lastmodified;

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


  
}
