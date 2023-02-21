<?php
namespace ChameleonSystem\CoreBundle\Entity;


class ShopUnitOfMeasurement {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - Name */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - Symbol / abbreviation */
private string $symbol = '', 
    // TCMSFieldLookup
/** @var ShopUnitOfMeasurement|null - Base unit */
private ?ShopUnitOfMeasurement $shopUnitOfMeasurement = null
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


  
    // TCMSFieldLookup
public function getShopUnitOfMeasurement(): ?ShopUnitOfMeasurement
{
    return $this->shopUnitOfMeasurement;
}

public function setShopUnitOfMeasurement(?ShopUnitOfMeasurement $shopUnitOfMeasurement): self
{
    $this->shopUnitOfMeasurement = $shopUnitOfMeasurement;

    return $this;
}


  
}
