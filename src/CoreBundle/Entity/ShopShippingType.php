<?php
namespace ChameleonSystem\CoreBundle\Entity;


class ShopShippingType {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - Name */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - Minimum amount of items affected */
private string $restrictToArticlesFrom = '', 
    // TCMSFieldVarchar
/** @var string - Maximum amount of items affected */
private string $restrictToArticlesTo = '', 
    // TCMSFieldVarchar
/** @var string - Minimum weight of affected items (grams) */
private string $restrictToWeightFrom = '', 
    // TCMSFieldVarchar
/** @var string - Maximum weight of affected items (grams) */
private string $restrictToWeightTo = ''  ) {}

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
public function getRestrictToArticlesFrom(): string
{
    return $this->restrictToArticlesFrom;
}
public function setRestrictToArticlesFrom(string $restrictToArticlesFrom): self
{
    $this->restrictToArticlesFrom = $restrictToArticlesFrom;

    return $this;
}


  
    // TCMSFieldVarchar
public function getRestrictToArticlesTo(): string
{
    return $this->restrictToArticlesTo;
}
public function setRestrictToArticlesTo(string $restrictToArticlesTo): self
{
    $this->restrictToArticlesTo = $restrictToArticlesTo;

    return $this;
}


  
    // TCMSFieldVarchar
public function getRestrictToWeightFrom(): string
{
    return $this->restrictToWeightFrom;
}
public function setRestrictToWeightFrom(string $restrictToWeightFrom): self
{
    $this->restrictToWeightFrom = $restrictToWeightFrom;

    return $this;
}


  
    // TCMSFieldVarchar
public function getRestrictToWeightTo(): string
{
    return $this->restrictToWeightTo;
}
public function setRestrictToWeightTo(string $restrictToWeightTo): self
{
    $this->restrictToWeightTo = $restrictToWeightTo;

    return $this;
}


  
}
