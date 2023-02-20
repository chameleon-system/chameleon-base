<?php
namespace ChameleonSystem\CoreBundle\Entity;


class CmsContentBox {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - Widget class */
private string $className = '', 
    // TCMSFieldVarchar
/** @var string - Widget class subfolder */
private string $classPath = 'Core', 
    // TCMSFieldVarchar
/** @var string - System name */
private string $systemName = '', 
    // TCMSFieldVarchar
/** @var string - Name */
private string $name = ''  ) {}

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
public function getClassName(): string
{
    return $this->className;
}
public function setClassName(string $className): self
{
    $this->className = $className;

    return $this;
}


  
    // TCMSFieldVarchar
public function getClassPath(): string
{
    return $this->classPath;
}
public function setClassPath(string $classPath): self
{
    $this->classPath = $classPath;

    return $this;
}


  
    // TCMSFieldVarchar
public function getSystemName(): string
{
    return $this->systemName;
}
public function setSystemName(string $systemName): self
{
    $this->systemName = $systemName;

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


  
}
