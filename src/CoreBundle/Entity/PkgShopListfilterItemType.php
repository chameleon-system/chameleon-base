<?php
namespace ChameleonSystem\CoreBundle\Entity;


class PkgShopListfilterItemType {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - Name */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - Filter element class */
private string $class = '', 
    // TCMSFieldVarchar
/** @var string - Class subtypes of the filter element */
private string $classSubtype = '', 
    // TCMSFieldVarchar
/** @var string - View of the filter element */
private string $view = ''  ) {}

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
public function getClass(): string
{
    return $this->class;
}
public function setClass(string $class): self
{
    $this->class = $class;

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


  
    // TCMSFieldVarchar
public function getView(): string
{
    return $this->view;
}
public function setView(string $view): self
{
    $this->view = $view;

    return $this;
}


  
}
