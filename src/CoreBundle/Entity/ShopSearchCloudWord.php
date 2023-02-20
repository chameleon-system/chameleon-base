<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopSearchCloudWord {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - Word */
private string $name = '', 
    // TCMSFieldDecimal
/** @var float - Percentage weight relative to real search terms */
private float $weight = 0  ) {}

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
public function getWeight(): float
{
    return $this->weight;
}
public function setWeight(float $weight): self
{
    $this->weight = $weight;

    return $this;
}


  
}
