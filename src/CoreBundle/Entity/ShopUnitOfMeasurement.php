<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopUnitOfMeasurement {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\ShopUnitOfMeasurement|null - Base unit */
private \ChameleonSystem\CoreBundle\Entity\ShopUnitOfMeasurement|null $shopUnitOfMeasurement = null,
/** @var null|string - Base unit */
private ?string $shopUnitOfMeasurementId = null
, 
    // TCMSFieldVarchar
/** @var string - Name */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - Symbol / abbreviation */
private string $symbol = '', 
    // TCMSFieldDecimal
/** @var float - Factor */
private float $factor = 0  ) {}

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


  
    // TCMSFieldVarchar
public function getSymbol(): string
{
    return $this->symbol;
}
public function setSymbol(string $symbol): self
{
    $this->symbol = $symbol;

    return $this;
}


  
    // TCMSFieldDecimal
public function getFactor(): float
{
    return $this->factor;
}
public function setFactor(float $factor): self
{
    $this->factor = $factor;

    return $this;
}


  
    // TCMSFieldLookup
public function getShopUnitOfMeasurement(): \ChameleonSystem\CoreBundle\Entity\ShopUnitOfMeasurement|null
{
    return $this->shopUnitOfMeasurement;
}
public function setShopUnitOfMeasurement(\ChameleonSystem\CoreBundle\Entity\ShopUnitOfMeasurement|null $shopUnitOfMeasurement): self
{
    $this->shopUnitOfMeasurement = $shopUnitOfMeasurement;
    $this->shopUnitOfMeasurementId = $shopUnitOfMeasurement?->getId();

    return $this;
}
public function getShopUnitOfMeasurementId(): ?string
{
    return $this->shopUnitOfMeasurementId;
}
public function setShopUnitOfMeasurementId(?string $shopUnitOfMeasurementId): self
{
    $this->shopUnitOfMeasurementId = $shopUnitOfMeasurementId;
    // todo - load new id
    //$this->shopUnitOfMeasurementId = $?->getId();

    return $this;
}



  
}
