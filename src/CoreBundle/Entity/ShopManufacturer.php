<?php
namespace ChameleonSystem\CoreBundle\Entity;


class ShopManufacturer {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - Name */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - Short description */
private string $descriptionShort = '', 
    // TCMSFieldVarchar
/** @var string - CSS file for manufacturer page */
private string $css = ''  ) {}

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
public function getDescriptionShort(): string
{
    return $this->descriptionShort;
}
public function setDescriptionShort(string $descriptionShort): self
{
    $this->descriptionShort = $descriptionShort;

    return $this;
}


  
    // TCMSFieldVarchar
public function getCss(): string
{
    return $this->css;
}
public function setCss(string $css): self
{
    $this->css = $css;

    return $this;
}


  
}
