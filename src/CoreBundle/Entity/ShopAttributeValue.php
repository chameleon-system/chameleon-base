<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\ShopAttribute;

class ShopAttributeValue {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldLookup
/** @var ShopAttribute|null - Belongs to the attribute */
private ?ShopAttribute $shopAttribute = null
, 
    // TCMSFieldVarchar
/** @var string - Value */
private string $name = ''  ) {}

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
public function getShopAttribute(): ?ShopAttribute
{
    return $this->shopAttribute;
}

public function setShopAttribute(?ShopAttribute $shopAttribute): self
{
    $this->shopAttribute = $shopAttribute;

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


  
}
