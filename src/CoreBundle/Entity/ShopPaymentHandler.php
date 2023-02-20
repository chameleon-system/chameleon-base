<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopPaymentHandler {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\ShopPaymentHandlerGroup|null - Belongs to payment provider */
private \ChameleonSystem\CoreBundle\Entity\ShopPaymentHandlerGroup|null $shopPaymentHandlerGroup = null,
/** @var null|string - Belongs to payment provider */
private ?string $shopPaymentHandlerGroupId = null
, 
    // TCMSFieldVarchar
/** @var string - Internal name for payment handler */
private string $name = '', 
    // TCMSFieldBoolean
/** @var bool - Block user selection */
private bool $blockUserSelection = false, 
    // TCMSFieldVarchar
/** @var string - Class name */
private string $class = '', 
    // TCMSFieldOption
/** @var string - Class type */
private string $classType = 'Core', 
    // TCMSFieldVarchar
/** @var string - Class subtype */
private string $classSubtype = '', 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\ShopPaymentHandlerParameter[] - Configuration settings */
private \Doctrine\Common\Collections\Collection $shopPaymentHandlerParameterCollection = new \Doctrine\Common\Collections\ArrayCollection()  ) {}

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
public function getShopPaymentHandlerGroup(): \ChameleonSystem\CoreBundle\Entity\ShopPaymentHandlerGroup|null
{
    return $this->shopPaymentHandlerGroup;
}
public function setShopPaymentHandlerGroup(\ChameleonSystem\CoreBundle\Entity\ShopPaymentHandlerGroup|null $shopPaymentHandlerGroup): self
{
    $this->shopPaymentHandlerGroup = $shopPaymentHandlerGroup;
    $this->shopPaymentHandlerGroupId = $shopPaymentHandlerGroup?->getId();

    return $this;
}
public function getShopPaymentHandlerGroupId(): ?string
{
    return $this->shopPaymentHandlerGroupId;
}
public function setShopPaymentHandlerGroupId(?string $shopPaymentHandlerGroupId): self
{
    $this->shopPaymentHandlerGroupId = $shopPaymentHandlerGroupId;
    // todo - load new id
    //$this->shopPaymentHandlerGroupId = $?->getId();

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
public function isBlockUserSelection(): bool
{
    return $this->blockUserSelection;
}
public function setBlockUserSelection(bool $blockUserSelection): self
{
    $this->blockUserSelection = $blockUserSelection;

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


  
    // TCMSFieldOption
public function getClassType(): string
{
    return $this->classType;
}
public function setClassType(string $classType): self
{
    $this->classType = $classType;

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


  
    // TCMSFieldPropertyTable
public function getShopPaymentHandlerParameterCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->shopPaymentHandlerParameterCollection;
}
public function setShopPaymentHandlerParameterCollection(\Doctrine\Common\Collections\Collection $shopPaymentHandlerParameterCollection): self
{
    $this->shopPaymentHandlerParameterCollection = $shopPaymentHandlerParameterCollection;

    return $this;
}


  
}
