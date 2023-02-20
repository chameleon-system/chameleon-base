<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopVat {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - Name */
private string $name = '', 
    // TCMSFieldDecimal
/** @var float - Percentage */
private float $vatPercent = 0  ) {}

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


  
}
