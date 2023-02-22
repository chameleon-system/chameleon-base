<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\CmsConfig;

class PkgCmsCounter {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - Name */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - System name */
private string $systemName = '', 
    // TCMSFieldLookup
/** @var CmsConfig|null - Owner */
private ?CmsConfig $ow = null
, 
    // TCMSFieldVarchar
/** @var string - Value */
private string $value = '0'  ) {}

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
public function getSystemName(): string
{
    return $this->systemName;
}
public function setSystemName(string $systemName): self
{
    $this->systemName = $systemName;

    return $this;
}


  
    // TCMSFieldLookup
public function getOw(): ?CmsConfig
{
    return $this->ow;
}

public function setOw(?CmsConfig $ow): self
{
    $this->ow = $ow;

    return $this;
}


  
    // TCMSFieldVarchar
public function getValue(): string
{
    return $this->value;
}
public function setValue(string $value): self
{
    $this->value = $value;

    return $this;
}


  
}
