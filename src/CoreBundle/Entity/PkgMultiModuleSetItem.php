<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\PkgMultiModuleSet;

class PkgMultiModuleSetItem {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - Module name */
private string $name = '', 
    // TCMSFieldLookupParentID
/** @var PkgMultiModuleSet|null - Belongs to set */
private ?PkgMultiModuleSet $pkgMultiModuleSet = null
, 
    // TCMSFieldVarchar
/** @var string - System name */
private string $systemName = ''  ) {}

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


  
    // TCMSFieldLookupParentID
public function getPkgMultiModuleSet(): ?PkgMultiModuleSet
{
    return $this->pkgMultiModuleSet;
}

public function setPkgMultiModuleSet(?PkgMultiModuleSet $pkgMultiModuleSet): self
{
    $this->pkgMultiModuleSet = $pkgMultiModuleSet;

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


  
}
