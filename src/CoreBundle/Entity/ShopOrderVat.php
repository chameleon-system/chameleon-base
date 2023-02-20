<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopOrderVat {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\ShopOrder|null - Belongs to order */
private \ChameleonSystem\CoreBundle\Entity\ShopOrder|null $shopOrder = null,
/** @var null|string - Belongs to order */
private ?string $shopOrderId = null
, 
    // TCMSFieldVarchar
/** @var string - Name */
private string $name = '', 
    // TCMSFieldDecimal
/** @var float - Percent */
private float $vatPercent = 0, 
    // TCMSFieldDecimal
/** @var float - Value for order */
private float $value = 0  ) {}

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


  
    // TCMSFieldDecimal
public function getVatPercent(): float
{
    return $this->vatPercent;
}
public function setVatPercent(float $vatPercent): self
{
    $this->vatPercent = $vatPercent;

    return $this;
}


  
    // TCMSFieldDecimal
public function getValue(): float
{
    return $this->value;
}
public function setValue(float $value): self
{
    $this->value = $value;

    return $this;
}


  
}
