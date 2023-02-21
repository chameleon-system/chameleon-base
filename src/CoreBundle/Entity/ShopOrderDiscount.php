<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\ShopOrder;

class ShopOrderDiscount {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldLookup
/** @var ShopOrder|null - Order ID */
private ?ShopOrder $shopOrder = null
, 
    // TCMSFieldVarchar
/** @var string - Discount ID */
private string $shopDiscountId = '', 
    // TCMSFieldVarchar
/** @var string - Name */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - Value */
private string $value = '', 
    // TCMSFieldVarchar
/** @var string - Value type */
private string $valuetype = '', 
    // TCMSFieldVarchar
/** @var string - Gratis article (name) */
private string $freearticleName = '', 
    // TCMSFieldVarchar
/** @var string - Gratis article (article number) */
private string $freearticleArticlenumber = '', 
    // TCMSFieldVarchar
/** @var string - Gratis article (ID) */
private string $freearticleId = ''  ) {}

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
public function getShopDiscountId(): string
{
    return $this->shopDiscountId;
}
public function setShopDiscountId(string $shopDiscountId): self
{
    $this->shopDiscountId = $shopDiscountId;

    return $this;
}


  
    // TCMSFieldVarchar
public function getName(): string
{
    return $this->name;
}
public function setName(string $name): self
{
    $this->name = $name;

    return $this;
}


  
    // TCMSFieldVarchar
public function getValue(): string
{
    return $this->value;
}
public function setValue(string $value): self
{
    $this->value = $value;

    return $this;
}


  
    // TCMSFieldVarchar
public function getValuetype(): string
{
    return $this->valuetype;
}
public function setValuetype(string $valuetype): self
{
    $this->valuetype = $valuetype;

    return $this;
}


  
    // TCMSFieldVarchar
public function getFreearticleName(): string
{
    return $this->freearticleName;
}
public function setFreearticleName(string $freearticleName): self
{
    $this->freearticleName = $freearticleName;

    return $this;
}


  
    // TCMSFieldVarchar
public function getFreearticleArticlenumber(): string
{
    return $this->freearticleArticlenumber;
}
public function setFreearticleArticlenumber(string $freearticleArticlenumber): self
{
    $this->freearticleArticlenumber = $freearticleArticlenumber;

    return $this;
}


  
    // TCMSFieldVarchar
public function getFreearticleId(): string
{
    return $this->freearticleId;
}
public function setFreearticleId(string $freearticleId): self
{
    $this->freearticleId = $freearticleId;

    return $this;
}


  
}
