<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopAttribute {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - Name */
private string $name = '', 
    // TCMSFieldBoolean
/** @var bool - System attributes */
private bool $isSystemAttribute = false, 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\ShopAttributeValue[] - Attribute values */
private \Doctrine\Common\Collections\Collection $shopAttributeValueCollection = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldVarchar
/** @var string - Internal name */
private string $systemName = '', 
    // TCMSFieldWYSIWYG
/** @var string - Description */
private string $description = ''  ) {}

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


  
    // TCMSFieldBoolean
public function isIsSystemAttribute(): bool
{
    return $this->isSystemAttribute;
}
public function setIsSystemAttribute(bool $isSystemAttribute): self
{
    $this->isSystemAttribute = $isSystemAttribute;

    return $this;
}


  
    // TCMSFieldPropertyTable
public function getShopAttributeValueCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->shopAttributeValueCollection;
}
public function setShopAttributeValueCollection(\Doctrine\Common\Collections\Collection $shopAttributeValueCollection): self
{
    $this->shopAttributeValueCollection = $shopAttributeValueCollection;

    return $this;
}


  
    // TCMSFieldVarchar
public function getSystemName(): string
{
    return $this->systemName;
}
public function setSystemName(string $systemName): self
{
    $this->systemName = $systemName;

    return $this;
}


  
    // TCMSFieldWYSIWYG
public function getDescription(): string
{
    return $this->description;
}
public function setDescription(string $description): self
{
    $this->description = $description;

    return $this;
}


  
}
