<?php
namespace ChameleonSystem\CoreBundle\Entity;


class ShopDiscount {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - Name */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - Min. amount of products affected */
private string $restrictToArticlesFrom = '', 
    // TCMSFieldVarchar
/** @var string - Max. amount of products affected */
private string $restrictToArticlesTo = ''  ) {}

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


  
}
