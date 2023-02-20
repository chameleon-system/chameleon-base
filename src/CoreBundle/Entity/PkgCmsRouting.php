<?php
namespace ChameleonSystem\CoreBundle\Entity;


class PkgCmsRouting {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - System name */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - Brief description */
private string $shortDescription = '', 
    // TCMSFieldVarchar
/** @var string - Resource */
private string $resource = '', 
    // TCMSFieldVarchar
/** @var string - System page */
private string $systemPageName = ''  ) {}

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
public function getShortDescription(): string
{
    return $this->shortDescription;
}
public function setShortDescription(string $shortDescription): self
{
    $this->shortDescription = $shortDescription;

    return $this;
}


  
    // TCMSFieldVarchar
public function getResource(): string
{
    return $this->resource;
}
public function setResource(string $resource): self
{
    $this->resource = $resource;

    return $this;
}


  
    // TCMSFieldVarchar
public function getSystemPageName(): string
{
    return $this->systemPageName;
}
public function setSystemPageName(string $systemPageName): self
{
    $this->systemPageName = $systemPageName;

    return $this;
}


  
}
