<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\ShopOrder;

class AmazonPaymentIdMapping {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldLookupParentID
/** @var ShopOrder|null - Belongs to order */
private ?ShopOrder $shopOrder = null
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
    // TCMSFieldVarchar
/** @var string - Type */
private string $type = '', 
    // TCMSFieldVarchar
/** @var string - Request mode */
private string $requestMode = '1'  ) {}

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


  
    // TCMSFieldVarchar
public function getType(): string
{
    return $this->type;
}
public function setType(string $type): self
{
    $this->type = $type;

    return $this;
}


  
    // TCMSFieldVarchar
public function getRequestMode(): string
{
    return $this->requestMode;
}
public function setRequestMode(string $requestMode): self
{
    $this->requestMode = $requestMode;

    return $this;
}


  
}
