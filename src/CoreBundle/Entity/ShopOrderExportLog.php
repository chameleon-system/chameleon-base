<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopOrderExportLog {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\ShopOrder|null - Belongs to order */
private \ChameleonSystem\CoreBundle\Entity\ShopOrder|null $shopOrder = null,
/** @var null|string - Belongs to order */
private ?string $shopOrderId = null
, 
    // TCMSFieldDateTimeNow
/** @var \DateTime|null - Created on */
private \DateTime|null $datecreated = null, 
    // TCMSFieldVarchar
/** @var string - IP */
private string $ip = '', 
    // TCMSFieldText
/** @var string - Data */
private string $data = '', 
    // TCMSFieldVarchar
/** @var string - Session ID */
private string $userSessionId = ''  ) {}

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



  
    // TCMSFieldDateTimeNow
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


  
    // TCMSFieldText
public function getData(): string
{
    return $this->data;
}
public function setData(string $data): self
{
    $this->data = $data;

    return $this;
}


  
    // TCMSFieldVarchar
public function getUserSessionId(): string
{
    return $this->userSessionId;
}
public function setUserSessionId(string $userSessionId): self
{
    $this->userSessionId = $userSessionId;

    return $this;
}


  
}
