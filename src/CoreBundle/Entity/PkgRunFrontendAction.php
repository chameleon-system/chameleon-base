<?php
namespace ChameleonSystem\CoreBundle\Entity;


class PkgRunFrontendAction {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - Class */
private string $class = '', 
    // TCMSFieldVarchar
/** @var string -  */
private string $randomKey = ''  ) {}

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
public function getRandomKey(): string
{
    return $this->randomKey;
}
public function setRandomKey(string $randomKey): self
{
    $this->randomKey = $randomKey;

    return $this;
}


  
}
