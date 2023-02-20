<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\CmsTblConf;

class CmsTblListClass {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - Alias name */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - Path to list class */
private string $classSubtype = '', 
    // TCMSFieldVarchar
/** @var string - Class name */
private string $classname = '', 
    // TCMSFieldLookupParentID
/** @var CmsTblConf|null - Belongs to */
private ?CmsTblConf $cmsTblConf = null
  ) {}

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
public function getClassname(): string
{
    return $this->classname;
}
public function setClassname(string $classname): self
{
    $this->classname = $classname;

    return $this;
}


  
    // TCMSFieldLookupParentID
public function getCmsTblConf(): ?CmsTblConf
{
    return $this->cmsTblConf;
}

public function setCmsTblConf(?CmsTblConf $cmsTblConf): self
{
    $this->cmsTblConf = $cmsTblConf;

    return $this;
}


  
}
