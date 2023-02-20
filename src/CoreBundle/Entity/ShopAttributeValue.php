<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopAttributeValue {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\ShopAttribute|null - Belongs to the attribute */
private \ChameleonSystem\CoreBundle\Entity\ShopAttribute|null $shopAttribute = null,
/** @var null|string - Belongs to the attribute */
private ?string $shopAttributeId = null
, 
    // TCMSFieldVarchar
/** @var string - Value */
private string $name = '', 
    // TCMSFieldPosition
/** @var int - Sorting */
private int $position = 0  ) {}

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
public function getShopAttribute(): \ChameleonSystem\CoreBundle\Entity\ShopAttribute|null
{
    return $this->shopAttribute;
}
public function setShopAttribute(\ChameleonSystem\CoreBundle\Entity\ShopAttribute|null $shopAttribute): self
{
    $this->shopAttribute = $shopAttribute;
    $this->shopAttributeId = $shopAttribute?->getId();

    return $this;
}
public function getShopAttributeId(): ?string
{
    return $this->shopAttributeId;
}
public function setShopAttributeId(?string $shopAttributeId): self
{
    $this->shopAttributeId = $shopAttributeId;
    // todo - load new id
    //$this->shopAttributeId = $?->getId();

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


  
    // TCMSFieldPosition
public function getPosition(): int
{
    return $this->position;
}
public function setPosition(int $position): self
{
    $this->position = $position;

    return $this;
}


  
}
