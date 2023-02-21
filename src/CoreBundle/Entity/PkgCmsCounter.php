<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\CmsConfig;
use ChameleonSystem\CoreBundle\Entity\CmsPortal;
use ChameleonSystem\CoreBundle\Entity\Shop;

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
    // TCMSFieldExtendedLookupMultiTable
/** @var CmsConfig|CmsPortal|Shop|null - Owner */
private ?CmsConfig|CmsPortal|Shop $ow = null
,
// TCMSFieldExtendedLookupMultiTable
/** @var string - Owner */
private string $ownerTableName = '', 
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


  
    // TCMSFieldExtendedLookupMultiTable
public function getOw(): ?CmsConfig|CmsPortal|Shop
{
    return $this->ow;
}

public function setOw(?CmsConfig|CmsPortal|Shop $ow): self
{
    $this->ow = $ow;

    return $this;
}
// TCMSFieldExtendedLookupMultiTable
public function getOwner(): string
{
    return $this->ownerTableName;
}
public function setOwner(string $ownerTableName): self
{
    $this->ownerTableName = $ownerTableName;

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
