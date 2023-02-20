<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgShopCurrency {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - Name */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - Symbol */
private string $symbol = '', 
    // TCMSFieldDecimal
/** @var float - Conversion factor */
private float $factor = 1, 
    // TCMSFieldUniqueMarker
/** @var bool - Is the base currency */
private bool $isBaseCurrency = false, 
    // TCMSFieldVarchar
/** @var string - ISO-4217 Code */
private string $iso4217 = ''  ) {}

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


  
    // TCMSFieldUniqueMarker
public function isIsBaseCurrency(): bool
{
    return $this->isBaseCurrency;
}
public function setIsBaseCurrency(bool $isBaseCurrency): self
{
    $this->isBaseCurrency = $isBaseCurrency;

    return $this;
}


  
    // TCMSFieldVarchar
public function getIso4217(): string
{
    return $this->iso4217;
}
public function setIso4217(string $iso4217): self
{
    $this->iso4217 = $iso4217;

    return $this;
}


  
}
