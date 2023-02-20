<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\ShopAttributeValue;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

class ShopAttribute {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - Name */
private string $name = '', 
    // TCMSFieldPropertyTable
/** @var Collection<int, shopAttributeValue> - Attribute values */
private Collection $shopAttributeValueCollection = new ArrayCollection()
, 
    // TCMSFieldVarchar
/** @var string - Internal name */
private string $systemName = ''  ) {}

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


  
    // TCMSFieldPropertyTable
/**
* @return Collection<int, shopAttributeValue>
*/
public function getShopAttributeValueCollection(): Collection
{
    return $this->shopAttributeValueCollection;
}

public function addShopAttributeValueCollection(shopAttributeValue $shopAttributeValue): self
{
    if (!$this->shopAttributeValueCollection->contains($shopAttributeValue)) {
        $this->shopAttributeValueCollection->add($shopAttributeValue);
        $shopAttributeValue->setShopAttribute($this);
    }

    return $this;
}

public function removeShopAttributeValueCollection(shopAttributeValue $shopAttributeValue): self
{
    if ($this->shopAttributeValueCollection->removeElement($shopAttributeValue)) {
        // set the owning side to null (unless already changed)
        if ($shopAttributeValue->getShopAttribute() === $this) {
            $shopAttributeValue->setShopAttribute(null);
        }
    }

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


  
}
