<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\Shop;
use ChameleonSystem\CoreBundle\Entity\ShopStockMessageTrigger;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

class ShopStockMessage {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - Class */
private string $className = '', 
    // TCMSFieldVarchar
/** @var string - Class subtype (path) */
private string $classSubtype = '', 
    // TCMSFieldLookup
/** @var Shop|null - Belongs to shop */
private ?Shop $shop = null
, 
    // TCMSFieldVarchar
/** @var string - Interface identifier */
private string $identifier = '', 
    // TCMSFieldVarchar
/** @var string - CSS class */
private string $class = '', 
    // TCMSFieldVarchar
/** @var string - Message */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - System name */
private string $internalName = '', 
    // TCMSFieldPropertyTable
/** @var Collection<int, shopStockMessageTrigger> - Stock messages */
private Collection $shopStockMessageTriggerCollection = new ArrayCollection()
, 
    // TCMSFieldVarchar
/** @var string - Google availability */
private string $googleAvailability = ''  ) {}

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
public function getClassName(): string
{
    return $this->className;
}
public function setClassName(string $className): self
{
    $this->className = $className;

    return $this;
}


  
    // TCMSFieldVarchar
public function getClassSubtype(): string
{
    return $this->classSubtype;
}
public function setClassSubtype(string $classSubtype): self
{
    $this->classSubtype = $classSubtype;

    return $this;
}


  
    // TCMSFieldLookup
public function getShop(): ?Shop
{
    return $this->shop;
}

public function setShop(?Shop $shop): self
{
    $this->shop = $shop;

    return $this;
}


  
    // TCMSFieldVarchar
public function getIdentifier(): string
{
    return $this->identifier;
}
public function setIdentifier(string $identifier): self
{
    $this->identifier = $identifier;

    return $this;
}


  
    // TCMSFieldVarchar
public function getClass(): string
{
    return $this->class;
}
public function setClass(string $class): self
{
    $this->class = $class;

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
public function getInternalName(): string
{
    return $this->internalName;
}
public function setInternalName(string $internalName): self
{
    $this->internalName = $internalName;

    return $this;
}


  
    // TCMSFieldPropertyTable
/**
* @return Collection<int, shopStockMessageTrigger>
*/
public function getShopStockMessageTriggerCollection(): Collection
{
    return $this->shopStockMessageTriggerCollection;
}

public function addShopStockMessageTriggerCollection(shopStockMessageTrigger $shopStockMessageTrigger): self
{
    if (!$this->shopStockMessageTriggerCollection->contains($shopStockMessageTrigger)) {
        $this->shopStockMessageTriggerCollection->add($shopStockMessageTrigger);
        $shopStockMessageTrigger->setShopStockMessage($this);
    }

    return $this;
}

public function removeShopStockMessageTriggerCollection(shopStockMessageTrigger $shopStockMessageTrigger): self
{
    if ($this->shopStockMessageTriggerCollection->removeElement($shopStockMessageTrigger)) {
        // set the owning side to null (unless already changed)
        if ($shopStockMessageTrigger->getShopStockMessage() === $this) {
            $shopStockMessageTrigger->setShopStockMessage(null);
        }
    }

    return $this;
}


  
    // TCMSFieldVarchar
public function getGoogleAvailability(): string
{
    return $this->googleAvailability;
}
public function setGoogleAvailability(string $googleAvailability): self
{
    $this->googleAvailability = $googleAvailability;

    return $this;
}


  
}
