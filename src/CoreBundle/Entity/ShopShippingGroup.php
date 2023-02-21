<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\ShopShippingGroupHandler;
use ChameleonSystem\CoreBundle\Entity\ShopVat;

class ShopShippingGroup {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - Name */
private string $name = '', 
    // TCMSFieldLookup
/** @var ShopShippingGroupHandler|null - Shipping group handler */
private ?ShopShippingGroupHandler $shopShippingGroupHandler = null
, 
    // TCMSFieldLookup
/** @var ShopVat|null - VAT group */
private ?ShopVat $shopVat = null
  ) {}

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
public function getName(): string
{
    return $this->name;
}
public function setName(string $name): self
{
    $this->name = $name;

    return $this;
}


  
    // TCMSFieldLookup
public function getShopShippingGroupHandler(): ?ShopShippingGroupHandler
{
    return $this->shopShippingGroupHandler;
}

public function setShopShippingGroupHandler(?ShopShippingGroupHandler $shopShippingGroupHandler): self
{
    $this->shopShippingGroupHandler = $shopShippingGroupHandler;

    return $this;
}


  
    // TCMSFieldLookup
public function getShopVat(): ?ShopVat
{
    return $this->shopVat;
}

public function setShopVat(?ShopVat $shopVat): self
{
    $this->shopVat = $shopVat;

    return $this;
}


  
}
